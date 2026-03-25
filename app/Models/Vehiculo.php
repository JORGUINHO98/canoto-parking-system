<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehiculo extends Model
{
    protected $fillable = [
        'cliente_id',
        'placa',
        'color',
        'modelo',
        'marca',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function ingresos(): HasMany
    {
        return $this->hasMany(Ingreso::class);
    }

    public function ingresoActivo(): ?Ingreso
    {
        return $this->ingresos()->whereNull('salida_at')->latest('entrada_at')->first();
    }
}
