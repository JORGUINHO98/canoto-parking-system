@extends('layouts.parking')

@section('title', 'Ingreso de vehículo')

@php
    $tipoEtiqueta = fn (?string $t) => match ($t) {
        \App\Models\Cliente::TIPO_ABONADO_VIP => 'Abonado VIP',
        \App\Models\Cliente::TIPO_ABONADO => 'Abonado',
        \App\Models\Cliente::TIPO_VISITANTE => 'Visitante',
        default => $t ?? '—',
    };
@endphp

@section('content')
    <div class="row mb-4 align-items-end">
        <div class="col-lg-8">
            <h1 class="h3 park-display mb-1">Control de ingreso</h1>
            <p class="text-secondary mb-0">Búsqueda por placa exacta o nombre parcial. Tarifa visitante: Bs.&nbsp;5 / hora o fracción.</p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            @if($parkingOpen)
                <span class="park-badge-open"><i class="bi bi-door-open me-1"></i>Ingresos permitidos ahora</span>
            @else
                <span class="park-badge-closed"><i class="bi bi-lock me-1"></i>Solo consulta · Ingresos {{ $parkingHoursLabel }}</span>
            @endif
        </div>
    </div>

    @error('horario')
        <div class="alert alert-warning border-0 shadow-sm" style="border-radius: var(--park-radius);">{{ $message }}</div>
    @enderror

    @if (! $parkingOpen)
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-start gap-2" style="border-radius: var(--park-radius);">
            <i class="bi bi-info-circle flex-shrink-0 mt-1"></i>
            <div>
                <strong>Fuera de horario de ingreso.</strong> El registro de entradas está habilitado de <strong>{{ $parkingHoursLabel }}</strong> (zona {{ config('app.timezone') }}). Puede buscar datos; los botones de ingreso permanecen deshabilitados.
            </div>
        </div>
    @endif

    <div class="card park-card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-search text-primary"></i> Búsqueda
        </div>
        <div class="card-body">
            <form action="{{ route('parking.ingreso') }}" method="get" class="row g-3">
                <div class="col-md-6">
                    <label for="placa" class="form-label">Placa</label>
                    <input type="text" name="placa" id="placa" value="{{ old('placa', $placaBusqueda) }}"
                           class="form-control font-monospace @error('placa') is-invalid @enderror"
                           placeholder="Ej. ABC1234" autocomplete="off">
                    @error('placa')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Coincidencia exacta; se normaliza a mayúsculas.</div>
                </div>
                <div class="col-md-6">
                    <label for="nombre" class="form-label">Nombre del cliente</label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $nombreBusqueda) }}"
                           class="form-control @error('nombre') is-invalid @enderror"
                           placeholder="Coincidencia parcial" autocomplete="off">
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-search me-1"></i>Buscar</button>
                    <a href="{{ route('parking.ingreso') }}" class="btn btn-light border rounded-pill px-4">Limpiar</a>
                    <a href="{{ route('parking.clientes.create') }}" class="btn btn-outline-secondary rounded-pill px-3 ms-md-auto"><i class="bi bi-person-plus me-1"></i>Cliente</a>
                    <a href="{{ route('parking.vehiculos.create') }}" class="btn btn-outline-secondary rounded-pill px-3"><i class="bi bi-truck-front me-1"></i>Vehículo</a>
                </div>
            </form>
        </div>
    </div>

    @if ($placaBusqueda && $placaExisteEnDb === true)
        <div class="alert alert-success border-0 py-3 shadow-sm" style="border-radius: var(--park-radius);">La placa <strong class="font-monospace">{{ $placaBusqueda }}</strong> está registrada.</div>
    @endif

    @if ($mostrarRegistroRapido)
        <div class="alert alert-warning border-0 py-3 shadow-sm" style="border-radius: var(--park-radius);">
            No existe la placa <strong class="font-monospace">{{ $placaBusqueda }}</strong>. Complete el registro rápido o use el <a href="{{ route('parking.clientes.create') }}">catálogo de clientes</a>.
        </div>

        <div class="card park-card border-warning border-opacity-50 mb-4">
            <div class="card-header bg-warning bg-opacity-10">Registro rápido + ingreso</div>
            <div class="card-body">
                <form action="{{ route('parking.store') }}" method="post" class="row g-3">
                    @csrf
                    <input type="hidden" name="modo" value="nuevo">
                    <input type="hidden" name="nueva_placa" value="{{ $placaBusqueda }}">
                    <div class="col-md-6">
                        <label class="form-label">Placa</label>
                        <input type="text" class="form-control font-monospace" value="{{ $placaBusqueda }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="nombre_cliente" class="form-label">Nombre del cliente</label>
                        <input type="text" name="nombre_cliente" id="nombre_cliente" class="form-control @error('nombre_cliente') is-invalid @enderror"
                               value="{{ old('nombre_cliente') }}" required>
                        @error('nombre_cliente')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="marca" class="form-label">Marca</label>
                        <input type="text" name="marca" id="marca" class="form-control @error('marca') is-invalid @enderror" value="{{ old('marca') }}">
                        @error('marca')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="text" name="modelo" id="modelo" class="form-control @error('modelo') is-invalid @enderror" value="{{ old('modelo') }}">
                        @error('modelo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="color" class="form-label">Color</label>
                        <input type="text" name="color" id="color" class="form-control @error('color') is-invalid @enderror" value="{{ old('color') }}">
                        @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="tipo_cliente" class="form-label">Tipo de cliente</label>
                        <select name="tipo_cliente" id="tipo_cliente" class="form-select @error('tipo_cliente') is-invalid @enderror" required>
                            @php $t = old('tipo_cliente', \App\Models\Cliente::TIPO_VISITANTE); @endphp
                            <option value="{{ \App\Models\Cliente::TIPO_VISITANTE }}" @selected($t === \App\Models\Cliente::TIPO_VISITANTE)>Visitante</option>
                            <option value="{{ \App\Models\Cliente::TIPO_ABONADO }}" @selected($t === \App\Models\Cliente::TIPO_ABONADO)>Abonado (Bs. 200 / mes)</option>
                            <option value="{{ \App\Models\Cliente::TIPO_ABONADO_VIP }}" @selected($t === \App\Models\Cliente::TIPO_ABONADO_VIP)>Abonado VIP (Bs. 400 / mes)</option>
                        </select>
                        @error('tipo_cliente')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="fecha_proximo_pago" class="form-label">Próximo pago (abonados)</label>
                        <input type="date" name="fecha_proximo_pago" id="fecha_proximo_pago"
                               class="form-control @error('fecha_proximo_pago') is-invalid @enderror"
                               value="{{ old('fecha_proximo_pago') }}">
                        @error('fecha_proximo_pago')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success rounded-pill px-4" @if(! $parkingOpen) disabled title="Fuera de horario" @endif>
                            Guardar y registrar ingreso
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($busquedaRealizada && $results && $results->isEmpty())
        <div class="alert alert-light border shadow-sm">No hay resultados para los criterios indicados.</div>
    @endif

    @if ($results && $results->isNotEmpty())
        <h2 class="h5 park-display mb-3">Resumen</h2>
        <div class="row g-3 mb-4">
            @foreach ($results as $row)
                <div class="col-md-6 col-xl-4">
                    <div class="card park-card h-100 border-top border-4 border-primary" style="border-top-color: #0369a1 !important;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="text-secondary small">Espacio sugerido</span>
                                <span class="badge rounded-pill bg-dark fs-6">{{ $row['piso_asignado'] ? 'Piso '.$row['piso_asignado'].' - Esp. '.$row['espacio_asignado'] : 'LLENO' }}</span>
                            </div>
                            <h3 class="h5 font-monospace mb-2">{{ $row['placa'] }}</h3>
                            <p class="mb-1 small"><strong>Cliente:</strong> {{ $row['cliente_nombre'] }}</p>
                            <p class="mb-1 small"><strong>Vehículo:</strong> {{ $row['marca'] ?? '—' }} {{ $row['modelo'] ?? '' }} · {{ $row['color'] ?? '—' }}</p>
                            <p class="mb-1 small"><strong>Tipo (registrado):</strong> {{ $tipoEtiqueta($row['tipo_registrado']) }}</p>
                            <p class="mb-2 small"><strong>Ingreso como:</strong>
                                <span class="badge rounded-pill {{ $row['tipo_efectivo'] === \App\Models\Cliente::TIPO_ABONADO_VIP ? 'text-bg-warning' : ($row['tipo_efectivo'] === \App\Models\Cliente::TIPO_ABONADO ? 'text-bg-primary' : 'text-bg-secondary') }}">
                                    {{ $tipoEtiqueta($row['tipo_efectivo']) }}
                                </span>
                            </p>
                            @if ($row['abono_vencido'])
                                <div class="alert alert-danger py-2 small mb-2">Abono vencido: se trata como visitante.</div>
                            @endif
                            @if ($row['alerta_visitante_recurrente'])
                                <span class="badge rounded-pill text-bg-info">Sugerencia: plan abonado</span>
                            @endif
                            @if ($row['ingreso_activo'])
                                <div class="alert alert-warning py-2 small mt-2 mb-0">Ingreso activo desde {{ $row['ingreso_activo']->entrada_at->format('d/m/Y H:i') }}.</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <h2 class="h5 park-display mb-3">Detalle</h2>
        <div class="d-none d-md-block card park-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-park">
                    <thead class="table-light">
                        <tr>
                            <th>Placa</th>
                            <th>Cliente</th>
                            <th>Vehículo</th>
                            <th>Tipo reg.</th>
                            <th>Tipo efectivo</th>
                            <th>Pago</th>
                            <th>Piso</th>
                            <th>Alertas</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($results as $row)
                            <tr>
                                <td class="fw-semibold font-monospace">{{ $row['placa'] }}</td>
                                <td>{{ $row['cliente_nombre'] }}</td>
                                <td class="small">{{ $row['marca'] ?? '—' }} {{ $row['modelo'] ?? '' }} · {{ $row['color'] ?? '—' }}</td>
                                <td>{{ $tipoEtiqueta($row['tipo_registrado']) }}</td>
                                <td>{{ $tipoEtiqueta($row['tipo_efectivo']) }}</td>
                                <td class="small">
                                    @if ($row['tipo_registrado'] !== \App\Models\Cliente::TIPO_VISITANTE)
                                        {{ $row['pago_al_dia'] ? 'Al día' : 'Vencido' }}
                                        @if ($row['fecha_proximo_pago'])
                                            <br><span class="text-muted">Vence {{ $row['fecha_proximo_pago']->format('d/m/Y') }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td><span class="badge rounded-pill bg-dark">{{ $row['piso_asignado'] ? 'Piso '.$row['piso_asignado'].' - Esp. '.$row['espacio_asignado'] : 'Lleno' }}</span></td>
                                <td class="small">
                                    @if ($row['abono_vencido'])
                                        <span class="badge text-bg-danger">Vencido</span>
                                    @endif
                                    @if ($row['alerta_visitante_recurrente'])
                                        <span class="badge text-bg-info">Abonado</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if (! $row['ingreso_activo'])
                                        <form action="{{ route('parking.store') }}" method="post" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="modo" value="existente">
                                            <input type="hidden" name="vehiculo_id" value="{{ $row['vehiculo_id'] }}">
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill" @if(! $parkingOpen) disabled @endif>Registrar ingreso</button>
                                        </form>
                                    @else
                                        <span class="text-muted small">Dentro</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-md-none">
            @foreach ($results as $row)
                <div class="card park-card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h3 class="h6 font-monospace mb-1">{{ $row['placa'] }}</h3>
                            <span class="badge rounded-pill bg-dark">{{ $row['piso_asignado'] ? 'Piso '.$row['piso_asignado'].' - Esp. '.$row['espacio_asignado'] : 'Lleno' }}</span>
                        </div>
                        <p class="small mb-1">{{ $row['cliente_nombre'] }}</p>
                        <p class="small text-secondary mb-2">{{ $row['marca'] ?? '—' }} {{ $row['modelo'] ?? '' }} · {{ $row['color'] ?? '—' }}</p>
                        <p class="small mb-1">Efectivo: {{ $tipoEtiqueta($row['tipo_efectivo']) }}</p>
                        @if (! $row['ingreso_activo'])
                            <form action="{{ route('parking.store') }}" method="post">
                                @csrf
                                <input type="hidden" name="modo" value="existente">
                                <input type="hidden" name="vehiculo_id" value="{{ $row['vehiculo_id'] }}">
                                <button type="submit" class="btn btn-success rounded-pill w-100" @if(! $parkingOpen) disabled @endif>Registrar ingreso</button>
                            </form>
                        @else
                            <p class="text-muted small mb-0">Vehículo en parqueo.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
