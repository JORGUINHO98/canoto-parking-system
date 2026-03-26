@php
    $v = $vehiculo ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-6">
        <label for="cliente_id" class="form-label">Cliente</label>
        <select name="cliente_id" id="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" required>
            @foreach ($clientes as $c)
                <option value="{{ $c->id }}" @selected(old('cliente_id', $v->cliente_id ?? null) == $c->id)>{{ $c->nombre }}</option>
            @endforeach
        </select>
        @error('cliente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="placa" class="form-label">Placa</label>
        <input type="text" name="placa" id="placa" class="form-control text-uppercase @error('placa') is-invalid @enderror"
               value="{{ old('placa', $v->placa ?? '') }}" required maxlength="16" autocomplete="off">
        @error('placa')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">6–8 caracteres alfanuméricos.</div>
    </div>
    <div class="col-md-4">
        <label for="marca" class="form-label">Marca</label>
        <input type="text" name="marca" id="marca" class="form-control @error('marca') is-invalid @enderror" value="{{ old('marca', $v->marca ?? '') }}">
        @error('marca')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="modelo" class="form-label">Modelo</label>
        <input type="text" name="modelo" id="modelo" class="form-control @error('modelo') is-invalid @enderror" value="{{ old('modelo', $v->modelo ?? '') }}">
        @error('modelo')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="color" class="form-label">Color</label>
        <input type="text" name="color" id="color" class="form-control @error('color') is-invalid @enderror" value="{{ old('color', $v->color ?? '') }}">
        @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
