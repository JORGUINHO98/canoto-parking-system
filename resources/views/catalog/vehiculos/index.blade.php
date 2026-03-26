@extends('layouts.parking')

@section('title', 'Vehículos')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 park-display mb-1">Vehículos</h1>
            <p class="text-secondary mb-0 small">Asociados a un cliente. No se elimina si hay ingreso activo o historial.</p>
        </div>
        <a href="{{ route('parking.vehiculos.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-1"></i> Nuevo vehículo
        </a>
    </div>

    <div class="card park-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-park">
                <thead class="table-light">
                    <tr>
                        <th>Placa</th>
                        <th>Cliente</th>
                        <th>Vehículo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vehiculos as $vehiculo)
                        <tr>
                            <td class="fw-semibold font-monospace">{{ $vehiculo->placa }}</td>
                            <td>{{ $vehiculo->cliente->nombre }}</td>
                            <td class="small text-muted">{{ $vehiculo->marca ?? '—' }} {{ $vehiculo->modelo ?? '' }} · {{ $vehiculo->color ?? '—' }}</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('parking.vehiculos.edit', $vehiculo) }}" class="btn btn-sm btn-outline-primary rounded-pill">Editar</a>
                                <form action="{{ route('parking.vehiculos.destroy', $vehiculo) }}" method="post" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar este vehículo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">No hay vehículos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($vehiculos->hasPages())
            <div class="card-footer bg-white border-top-0">{{ $vehiculos->links() }}</div>
        @endif
    </div>
@endsection
