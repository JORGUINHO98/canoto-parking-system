@extends('layouts.parking')

@section('title', 'Editar cliente')

@section('content')
    <div class="mb-4">
        <a href="{{ route('parking.clientes.index') }}" class="text-decoration-none small text-muted"><i class="bi bi-arrow-left me-1"></i>Volver al listado</a>
        <h1 class="h3 park-display mt-2 mb-1">Editar cliente</h1>
    </div>

    <div class="card park-card">
        <div class="card-header">{{ $cliente->nombre }}</div>
        <div class="card-body">
            <form action="{{ route('parking.clientes.update', $cliente) }}" method="post">
                @csrf
                @method('PUT')
                @include('catalog.clientes._form', ['cliente' => $cliente])
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Actualizar</button>
                    <a href="{{ route('parking.clientes.index') }}" class="btn btn-light border rounded-pill px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
