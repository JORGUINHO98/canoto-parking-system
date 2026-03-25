<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    public const TIPO_VISITANTE = 'visitante';

    public const TIPO_ABONADO = 'abonado';

    public const TIPO_ABONADO_VIP = 'abonado_vip';

    protected $fillable = [
        'nombre',
        'tipo_cliente',
        'fecha_proximo_pago',
        'telefono',
    ];

    protected function casts(): array
    {
        return [
            'fecha_proximo_pago' => 'date',
        ];
    }

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehiculo::class);
    }

    public function ingresos(): HasMany
    {
        return $this->hasMany(Ingreso::class);
    }

    public function isAbonadoActivo(): bool
    {
        if ($this->tipo_cliente === self::TIPO_VISITANTE) {
            return false;
        }

        return $this->fecha_proximo_pago !== null
            && $this->fecha_proximo_pago->greaterThanOrEqualTo(today());
    }
}
