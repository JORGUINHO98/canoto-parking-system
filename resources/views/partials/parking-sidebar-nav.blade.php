@php
    $isIngreso = request()->routeIs('parking.ingreso', 'parking.search');
    $isSalida = request()->routeIs('parking.salida', 'parking.salida.process');
    $isClientes = request()->routeIs('parking.clientes.*');
    $isVehiculos = request()->routeIs('parking.vehiculos.*');
@endphp
<nav class="nav flex-column px-1">
    <div class="small-label">Operación</div>
    <a class="nav-link {{ $isIngreso ? 'active' : '' }}" href="{{ route('parking.ingreso') }}">
        <i class="bi bi-box-arrow-in-right"></i> Control de ingreso
    </a>
    <a class="nav-link {{ $isSalida ? 'active' : '' }}" href="{{ route('parking.salida') }}">
        <i class="bi bi-receipt"></i> Salida y facturación
    </a>
    <div class="small-label">Catálogo</div>
    <a class="nav-link {{ $isClientes ? 'active' : '' }}" href="{{ route('parking.clientes.index') }}">
        <i class="bi bi-people"></i> Clientes
    </a>
    <a class="nav-link {{ $isVehiculos ? 'active' : '' }}" href="{{ route('parking.vehiculos.index') }}">
        <i class="bi bi-truck-front"></i> Vehículos
    </a>
</nav>
