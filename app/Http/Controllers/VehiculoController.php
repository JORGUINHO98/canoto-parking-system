<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Support\Placa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VehiculoController extends Controller
{
    public function index(): View
    {
        $vehiculos = Vehiculo::query()
            ->with('cliente')
            ->orderBy('placa')
            ->paginate(12)
            ->withQueryString();

        return view('catalog.vehiculos.index', compact('vehiculos'));
    }

    public function create(): View
    {
        $clientes = Cliente::query()->orderBy('nombre')->get();

        return view('catalog.vehiculos.create', compact('clientes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $placa = Placa::normalizar((string) $request->input('placa', ''));
        $request->merge(['placa_normalized' => $placa]);

        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'placa_normalized' => ['required', 'regex:/^[A-Z0-9]{6,8}$/', 'unique:vehiculos,placa'],
            'color' => ['nullable', 'string', 'max:64'],
            'modelo' => ['nullable', 'string', 'max:120'],
            'marca' => ['nullable', 'string', 'max:120'],
        ], [], [
            'placa_normalized' => 'placa',
        ]);

        Vehiculo::query()->create([
            'cliente_id' => $validated['cliente_id'],
            'placa' => $placa,
            'color' => $validated['color'] ?? null,
            'modelo' => $validated['modelo'] ?? null,
            'marca' => $validated['marca'] ?? null,
        ]);

        return redirect()
            ->route('parking.vehiculos.index')
            ->with('status', 'Vehículo registrado correctamente.');
    }

    public function edit(Vehiculo $vehiculo): View
    {
        $clientes = Cliente::query()->orderBy('nombre')->get();

        return view('catalog.vehiculos.edit', compact('vehiculo', 'clientes'));
    }

    public function update(Request $request, Vehiculo $vehiculo): RedirectResponse
    {
        $placa = Placa::normalizar((string) $request->input('placa', ''));
        $request->merge(['placa_normalized' => $placa]);

        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'placa_normalized' => [
                'required',
                'regex:/^[A-Z0-9]{6,8}$/',
                Rule::unique('vehiculos', 'placa')->ignore($vehiculo->id),
            ],
            'color' => ['nullable', 'string', 'max:64'],
            'modelo' => ['nullable', 'string', 'max:120'],
            'marca' => ['nullable', 'string', 'max:120'],
        ], [], [
            'placa_normalized' => 'placa',
        ]);

        $vehiculo->update([
            'cliente_id' => $validated['cliente_id'],
            'placa' => $placa,
            'color' => $validated['color'] ?? null,
            'modelo' => $validated['modelo'] ?? null,
            'marca' => $validated['marca'] ?? null,
        ]);

        return redirect()
            ->route('parking.vehiculos.index')
            ->with('status', 'Vehículo actualizado.');
    }

    public function destroy(Vehiculo $vehiculo): RedirectResponse
    {
        if ($vehiculo->ingresoActivo() !== null) {
            return back()->withErrors([
                'delete' => 'No se puede eliminar: el vehículo tiene un ingreso activo. Registre la salida primero.',
            ]);
        }

        if ($vehiculo->ingresos()->exists()) {
            return back()->withErrors([
                'delete' => 'No se puede eliminar: el vehículo tiene historial de ingresos en el sistema.',
            ]);
        }

        $vehiculo->delete();

        return redirect()
            ->route('parking.vehiculos.index')
            ->with('status', 'Vehículo eliminado.');
    }
}
