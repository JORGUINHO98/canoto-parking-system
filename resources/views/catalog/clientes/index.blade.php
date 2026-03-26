@extends('layouts.parking')

@section('title', 'Clientes')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 park-display mb-1">Clientes</h1>
            <p class="text-secondary mb-0 small">Alta, edición y baja. No se puede eliminar un cliente con vehículos asignados.</p>
        </div>
        <a href="{{ route('parking.clientes.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-person-plus me-1"></i> Nuevo cliente
        </a>
    </div>

    <div class="card park-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-park">
                <thead class="table-light">
                    <tr>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Próximo pago</th>
                        <th>Vehículos</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clientes as $cliente)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $cliente->nombre }}</div>
                                @if($cliente->telefono)
                                    <div class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $cliente->telefono }}</div>
                                @endif
                            </td>
                            <td>
                                @if($cliente->tipo_cliente === \App\Models\Cliente::TIPO_ABONADO_VIP)
                                    <span class="badge rounded-pill text-bg-warning">VIP</span>
                                @elseif($cliente->tipo_cliente === \App\Models\Cliente::TIPO_ABONADO)
                                    <span class="badge rounded-pill text-bg-primary">Abonado</span>
                                @else
                                    <span class="badge rounded-pill text-bg-secondary">Visitante</span>
                                @endif
                            </td>
                            <td class="small">
                                @if($cliente->fecha_proximo_pago)
                                    {{ $cliente->fecha_proximo_pago->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $cliente->vehiculos_count }}</span></td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('parking.clientes.edit', $cliente) }}" class="btn btn-sm btn-outline-primary rounded-pill">Editar</a>
                                <form action="{{ route('parking.clientes.destroy', $cliente) }}" method="post" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar este cliente?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">No hay clientes registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($clientes->hasPages())
            <div class="card-footer bg-white border-top-0">{{ $clientes->links() }}</div>
        @endif
    </div>
@endsection
