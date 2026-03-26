<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Ingreso;
use App\Models\Vehiculo;
use App\Support\Placa;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function salida(): View
    {
        return view('parking.salida');
    }

    public function procesarSalida(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'placa' => ['required', 'string', 'max:32'],
        ]);

        $placaNorm = Placa::normalizar($validated['placa']);

        if (! preg_match('/^[A-Z0-9]{6,8}$/', $placaNorm)) {
            throw ValidationException::withMessages([
                'placa' => ['El formato de placa no es válido (6–8 caracteres alfanuméricos).'],
            ]);
        }

        $vehiculo = Vehiculo::where('placa', $placaNorm)->first();

        if ($vehiculo === null) {
            return back()->withErrors(['placa' => 'No hay vehículo registrado con esa placa.'])->withInput();
        }

        $ingreso = Ingreso::query()
            ->where('vehiculo_id', $vehiculo->id)
            ->whereNull('salida_at')
            ->latest('entrada_at')
            ->first();

        if ($ingreso === null) {
            return back()->withErrors(['placa' => 'No hay un ingreso activo para esta placa.'])->withInput();
        }

        $ingreso->load(['vehiculo', 'cliente']);

        $ingreso->salida_at = now();
        $ingreso->save();
        $total = $this->calcularTotalSalida($ingreso);
        $ingreso->total_bs = $total;
        $ingreso->save();

        $esSinCobro = in_array(
            $ingreso->tipo_efectivo,
            [Cliente::TIPO_ABONADO, Cliente::TIPO_ABONADO_VIP],
            true
        );

        $horasCobradas = $esSinCobro
            ? null
            : $this->horasCobradasVisitante($ingreso->entrada_at, $ingreso->salida_at);

        return view('parking.salida', [
            'ticket' => $ingreso->fresh(['vehiculo', 'cliente']),
            'total_bs' => $total,
            'horas_cobradas' => $horasCobradas,
            'es_autorizacion_sin_cobro' => $esSinCobro,
        ]);
    }

    private function calcularTotalSalida(Ingreso $ingreso): float
    {
        if (in_array($ingreso->tipo_efectivo, [
            Cliente::TIPO_ABONADO,
            Cliente::TIPO_ABONADO_VIP,
        ], true)) {
            return 0.0;
        }

        $horas = $this->horasCobradasVisitante($ingreso->entrada_at, $ingreso->salida_at);

        return round($horas * ParkingController::TARIFA_VISITANTE_HORA_BS, 2);
    }

    private function horasCobradasVisitante(CarbonInterface $entrada, CarbonInterface $salida): int
    {
        $minutos = max(1, $entrada->diffInMinutes($salida));

        return (int) ceil($minutos / 60);
    }
}
