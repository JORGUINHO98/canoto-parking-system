@php
    $c = $cliente ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-6">
        <label for="nombre" class="form-label">Nombre completo</label>
        <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $c->nombre ?? '') }}" required maxlength="191">
        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="telefono" class="form-label">Teléfono</label>
        <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror"
               value="{{ old('telefono', $c->telefono ?? '') }}" maxlength="32" placeholder="Opcional">
        @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="tipo_cliente" class="form-label">Tipo de cliente</label>
        @php $t = old('tipo_cliente', $c->tipo_cliente ?? \App\Models\Cliente::TIPO_VISITANTE); @endphp
        <select name="tipo_cliente" id="tipo_cliente" class="form-select @error('tipo_cliente') is-invalid @enderror" required>
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
               value="{{ old('fecha_proximo_pago', isset($c->fecha_proximo_pago) ? $c->fecha_proximo_pago->format('Y-m-d') : '') }}">
        @error('fecha_proximo_pago')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Obligatorio para Abonado y VIP.</div>
    </div>
</div>
