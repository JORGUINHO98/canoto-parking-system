@extends('layouts.parking')

@section('title', 'Salida y ticket')

@section('content')
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="h3 park-display mb-1">Salida y facturación</h1>
            <p class="text-secondary mb-0">Abonados y VIP: Bs. 0. Visitantes: cobro por tiempo de estadía (fracción al alza).</p>
        </div>
    </div>

    @if (! isset($ticket))
        <div class="card park-card" style="max-width: 36rem;">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-upc-scan text-primary"></i> Ticket de salida
            </div>
            <div class="card-body">
                <form action="{{ route('parking.salida.process') }}" method="post" class="row g-3">
                    @csrf
                    <div class="col-12">
                        <label for="placa" class="form-label">Placa</label>
                        <input type="text" name="placa" id="placa" class="form-control font-monospace text-uppercase @error('placa') is-invalid @enderror"
                               value="{{ old('placa') }}" placeholder="Ej. ABC1234" required autocomplete="off">
                        @error('placa')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-receipt me-1"></i>Generar ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="card park-card border-success border-2">
                    <div class="card-header text-white fw-semibold d-flex align-items-center gap-2" style="background: linear-gradient(135deg,#059669,#10b981);">
                        <i class="bi bi-check-circle-fill"></i>
                        @if ($es_autorizacion_sin_cobro)
                            Autorización sin cobro
                        @else
                            Ticket visitante
                        @endif
                    </div>
                    <div class="card-body">
                        <dl class="row small mb-0">
                            <dt class="col-sm-4 text-muted">Placa</dt>
                            <dd class="col-sm-8 fw-bold font-monospace">{{ $ticket->vehiculo->placa }}</dd>
                            <dt class="col-sm-4 text-muted">Cliente</dt>
                            <dd class="col-sm-8">{{ $ticket->cliente->nombre }}</dd>
                            <dt class="col-sm-4 text-muted">Tipo cobro</dt>
                            <dd class="col-sm-8">{{ $ticket->tipo_efectivo === \App\Models\Cliente::TIPO_ABONADO_VIP ? 'Abonado VIP' : ($ticket->tipo_efectivo === \App\Models\Cliente::TIPO_ABONADO ? 'Abonado' : 'Visitante') }}</dd>
                            <dt class="col-sm-4 text-muted">Entrada</dt>
                            <dd class="col-sm-8">{{ $ticket->entrada_at->format('d/m/Y H:i') }}</dd>
                            <dt class="col-sm-4 text-muted">Salida</dt>
                            <dd class="col-sm-8">{{ $ticket->salida_at?->format('d/m/Y H:i') }}</dd>
                            @if (! $es_autorizacion_sin_cobro)
                                <dt class="col-sm-4 text-muted">Concepto</dt>
                                <dd class="col-sm-8">{{ $horas_cobradas }} h × Bs. {{ number_format(\App\Http\Controllers\ParkingController::TARIFA_VISITANTE_HORA_BS, 2) }}</dd>
                            @endif
                            <dt class="col-sm-4 text-muted">Total</dt>
                            <dd class="col-sm-8 fs-4 fw-bold park-display">Bs. {{ number_format($total_bs, 2) }}</dd>
                        </dl>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="{{ route('parking.salida') }}" class="btn btn-outline-primary rounded-pill">Nueva salida</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
