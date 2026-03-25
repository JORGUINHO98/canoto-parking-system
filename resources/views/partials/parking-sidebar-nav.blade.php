@php
    $isIngreso = request()->routeIs('parking.ingreso', 'parking.search');
    $isSalida = request()->routeIs('parking.salida', 'parking.salida.process');
@endphp
<nav class="nav nav-pills flex-column px-2">
    <a class="nav-link {{ $isIngreso ? 'active' : '' }}" href="{{ route('parking.ingreso') }}">Control de ingreso</a>
    <a class="nav-link {{ $isSalida ? 'active' : '' }}" href="{{ route('parking.salida') }}">Salida y facturación</a>
</nav>
