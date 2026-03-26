<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingreso extends Model
{
    protected $fillable = [
        'vehiculo_id',
        'cliente_id',
        'piso',
        'entrada_at',
        'salida_at',
        'tipo_registrado',
        'tipo_efectivo',
        'abono_vencido_tratado_como_visitante',
        'total_bs',
    ];

    protected function casts(): array
    {
        return [
            'entrada_at' => 'datetime',
            'salida_at' => 'datetime',
            'abono_vencido_tratado_como_visitante' => 'boolean',
            'total_bs' => 'decimal:2',
        ];
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
