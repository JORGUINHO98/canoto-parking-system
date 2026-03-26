@extends('layouts.parking')

@section('title', 'Editar vehículo')

@section('content')
    <div class="mb-4">
        <a href="{{ route('parking.vehiculos.index') }}" class="text-decoration-none small text-muted"><i class="bi bi-arrow-left me-1"></i>Volver al listado</a>
        <h1 class="h3 park-display mt-2 mb-1">Editar vehículo</h1>
    </div>

    <div class="card park-card">
        <div class="card-header font-monospace">{{ $vehiculo->placa }}</div>
        <div class="card-body">
            <form action="{{ route('parking.vehiculos.update', $vehiculo) }}" method="post">
                @csrf
                @method('PUT')
                @include('catalog.vehiculos._form', ['clientes' => $clientes, 'vehiculo' => $vehiculo])
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Actualizar</button>
                    <a href="{{ route('parking.vehiculos.index') }}" class="btn btn-light border rounded-pill px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
