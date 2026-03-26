<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(): View
    {
        $clientes = Cliente::query()
            ->withCount('vehiculos')
            ->orderBy('nombre')
            ->paginate(12)
            ->withQueryString();

        return view('catalog.clientes.index', compact('clientes'));
    }

    public function create(): View
    {
        return view('catalog.clientes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:191'],
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
            'telefono' => ['nullable', 'string', 'max:32'],
        ]);

        Cliente::query()->create([
            'nombre' => $validated['nombre'],
            'tipo_cliente' => $validated['tipo_cliente'],
            'fecha_proximo_pago' => $validated['fecha_proximo_pago'] ?? null,
            'telefono' => $validated['telefono'] ?? null,
        ]);

        return redirect()
            ->route('parking.clientes.index')
            ->with('status', 'Cliente registrado correctamente.');
    }

    public function edit(Cliente $cliente): View
    {
        return view('catalog.clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:191'],
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
            'telefono' => ['nullable', 'string', 'max:32'],
        ]);

        $cliente->update([
            'nombre' => $validated['nombre'],
            'tipo_cliente' => $validated['tipo_cliente'],
            'fecha_proximo_pago' => $validated['fecha_proximo_pago'] ?? null,
            'telefono' => $validated['telefono'] ?? null,
        ]);

        return redirect()
            ->route('parking.clientes.index')
            ->with('status', 'Cliente actualizado.');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        // Soft delete vehiculos first (cascade)
        $cliente->vehiculos()->delete();

        $cliente->delete();

        return redirect()
            ->route('parking.clientes.index')
            ->with('status', 'Cliente eliminado lógicamente.');
    }

    public function restore(Cliente $cliente): RedirectResponse
    {
        // Restore vehiculos first, then cliente
        $cliente->vehiculos()->restore();
        $cliente->restore();

        return redirect()
            ->route('parking.clientes.index')
            ->with('status', 'Cliente restaurado.');
    }
}

