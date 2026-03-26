<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Ingreso;
use App\Models\Vehiculo;
use App\Support\ParkingHours;
use App\Support\Placa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ParkingController extends Controller
{
    public const TARIFA_VISITANTE_HORA_BS = 5.00;

    public function ingreso(Request $request): View
    {
        $placaRaw = $request->input('placa');
        $nombreRaw = $request->input('nombre');
        $nombre = is_string($nombreRaw) ? trim($nombreRaw) : '';

        $busquedaRealizada = $request->has('placa') || $request->has('nombre');

        if (! $busquedaRealizada) {
            return view('parking.ingreso', [
                'results' => null,
                'placaBusqueda' => null,
                'nombreBusqueda' => null,
                'placaExisteEnDb' => null,
                'mostrarRegistroRapido' => false,
                'busquedaRealizada' => false,
            ]);
        }

        $placaNorm = $placaRaw !== null && $placaRaw !== ''
            ? Placa::normalizar((string) $placaRaw)
            : null;

        $request->merge([
            'placa_normalized' => $placaNorm,
        ]);

        $request->validate(
            [
                'placa' => ['nullable', 'string', 'max:32'],
                'nombre' => ['nullable', 'string', 'max:191'],
                'placa_normalized' => [
                    Rule::requiredIf(fn () => $nombre === ''),
                ],
            ],
            [
                'placa_normalized.required' => 'Indique placa o nombre del cliente.',
            ],
            [
                'placa_normalized' => 'placa',
            ]
        );

        if ($placaNorm !== null && $placaNorm !== '' && ! Placa::esValida($placaNorm)) {
            throw ValidationException::withMessages([
                'placa' => ['El formato de placa no es válido (6–8 caracteres alfanuméricos).'],
            ]);
        }

        $vehiculos = $this->buscarVehiculos($placaNorm, $nombre !== '' ? $nombre : null);

        $placaExiste = $placaNorm !== null && $placaNorm !== ''
            ? Vehiculo::where('placa', $placaNorm)->exists()
            : null;

        $results = $vehiculos->map(fn (Vehiculo $v) => $this->mapearResultadoBusqueda($v));

        return view('parking.ingreso', [
            'results' => $results,
            'placaBusqueda' => $placaNorm,
            'nombreBusqueda' => $nombre !== '' ? $nombre : null,
            'placaExisteEnDb' => $placaExiste,
            'mostrarRegistroRapido' => (bool) ($placaNorm && ! $placaExiste && Placa::esValida((string) $placaNorm)),
            'busquedaRealizada' => true,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Corregido: Eliminados caracteres \n y arreglada la estructura
        if (! ParkingHours::isOpenForIngresso()) {
            return back()->withErrors([
                'horario' => ParkingHours::closedMessage(),
            ])->withInput();
        }

        $modo = $request->input('modo', 'existente');

        if ($modo === 'nuevo') {
            $placa = Placa::normalizar((string) $request->input('nueva_placa', ''));

            $request->merge(['nueva_placa_normalized' => $placa]);

            $validated = $request->validate([
                'nombre_cliente' => ['required', 'string', 'max:191'],
                'nueva_placa_normalized' => ['required', 'regex:/^[A-Z0-9]{6,8}$/', 'unique:vehiculos,placa'],
                'color' => ['nullable', 'string', 'max:64'],
                'modelo' => ['nullable', 'string', 'max:120'],
                'marca' => ['nullable', 'string', 'max:120'],
                'tipo_cliente' => ['required', Rule::in([
                    Cliente::TIPO_VISITANTE,
                    Cliente::TIPO_ABONADO,
                    Cliente::TIPO_ABONADO_VIP,
                ])],
                'fecha_proximo_pago' => [
                    'nullable',
                    'date',
                    Rule::requiredIf(fn () => in_array(
                        (string) $request->input('tipo_cliente'),
                        [Cliente::TIPO_ABONADO, Cliente::TIPO_ABONADO_VIP],
                        true
                    )),
                ],
            ], [], [
                'nueva_placa_normalized' => 'placa',
            ]);

            $cliente = Cliente::create([
                'nombre' => $validated['nombre_cliente'],
                'tipo_cliente' => $validated['tipo_cliente'],
                'fecha_proximo_pago' => $validated['fecha_proximo_pago'] ?? null,
            ]);

            $vehiculo = Vehiculo::create([
                'cliente_id' => $cliente->id,
                'placa' => $placa,
                'color' => $validated['color'] ?? null,
                'modelo' => $validated['modelo'] ?? null,
                'marca' => $validated['marca'] ?? null,
            ]);
        } else {
            $validated = $request->validate([
                'vehiculo_id' => ['required', 'exists:vehiculos,id'],
            ]);

            $vehiculo = Vehiculo::with('cliente')->findOrFail($validated['vehiculo_id']);
            $cliente = $vehiculo->cliente;
        }

        // Corregido: Arreglado el cierre del if y el return
        if ($vehiculo->ingresoActivo() !== null) {
            return back()->withErrors([
                'ingreso' => 'Este vehículo ya tiene un ingreso activo. Procese la salida primero.',
            ])->withInput();
        }

        [$tipoEfectivo, $vencido] = $this->resolverTipoEfectivo($cliente);
        $piso = $this->asignarPiso($tipoEfectivo, $vehiculo->placa);

        Ingreso::create([
            'vehiculo_id' => $vehiculo->id,
            'cliente_id' => $cliente->id,
            'piso' => $piso,
            'entrada_at' => now(), // Usa America/La_Paz según tu config
            'tipo_registrado' => $cliente->tipo_cliente,
            'tipo_efectivo' => $tipoEfectivo,
            'abono_vencido_tratado_como_visitante' => $vencido,
            'total_bs' => null,
        ]);

        return redirect()
            ->route('parking.ingreso')
            ->with('status', 'Ingreso registrado. Piso asignado: '.$piso.'.');
    }

    private function buscarVehiculos(?string $placaNorm, ?string $nombre): Collection
    {
        $q = Vehiculo::query()->with('cliente');

        if ($placaNorm !== null && $placaNorm !== '') {
            $q->where('placa', $placaNorm);
        }

        if ($nombre !== null && $nombre !== '') {
            $q->whereHas('cliente', function ($c) use ($nombre): void {
                $c->where('nombre', 'like', '%'.$nombre.'%');
            });
        }

        return $q->orderBy('placa')->get();
    }

    private function mapearResultadoBusqueda(Vehiculo $v): array
    {
        $cliente = $v->cliente;
        [$tipoEfectivo, $vencido] = $this->resolverTipoEfectivo($cliente);
        $piso = $this->asignarPiso($tipoEfectivo, $v->placa);

        return [
            'vehiculo_id' => $v->id,
            'placa' => $v->placa,
            'color' => $v->color,
            'modelo' => $v->modelo,
            'marca' => $v->marca,
            'cliente_nombre' => $cliente->nombre,
            'tipo_registrado' => $cliente->tipo_cliente,
            'tipo_efectivo' => $tipoEfectivo,
            'pago_al_dia' => $cliente->isAbonadoActivo(),
            'fecha_proximo_pago' => $cliente->fecha_proximo_pago,
            'piso_asignado' => $piso,
            'abono_vencido' => $vencido,
            'alerta_visitante_recurrente' => $tipoEfectivo === Cliente::TIPO_VISITANTE
                && $this->esVisitanteRecurrente($cliente),
            'ingreso_activo' => $v->ingresoActivo(),
        ];
    }

    private function resolverTipoEfectivo(Cliente $cliente): array
    {
        if (in_array($cliente->tipo_cliente, [
            Cliente::TIPO_ABONADO,
            Cliente::TIPO_ABONADO_VIP,
        ], true) && ! $cliente->isAbonadoActivo()) {
            return [Cliente::TIPO_VISITANTE, true];
        }

        if (in_array($cliente->tipo_cliente, [
            Cliente::TIPO_ABONADO,
            Cliente::TIPO_ABONADO_VIP,
        ], true)) {
            return [$cliente->tipo_cliente, false];
        }

        return [Cliente::TIPO_VISITANTE, false];
    }

    private function esVisitanteRecurrente(Cliente $cliente): bool
    {
        return Ingreso::query()
            ->where('cliente_id', $cliente->id)
            ->where('tipo_efectivo', Cliente::TIPO_VISITANTE)
            ->whereNotNull('salida_at')
            ->exists();
    }

    private function asignarPiso(string $tipoEfectivo, string $placa): int
    {
        if ($tipoEfectivo === Cliente::TIPO_ABONADO_VIP) {
            return 1;
        }

        $hash = crc32($placa);
        return 2 + ($hash % 4);
    }
}