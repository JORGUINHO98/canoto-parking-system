<?php

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class ParkingHours
{
    /** Ingresos permitidos desde las 07:00 hasta las 23:59 (hora local de la app). */
    public static function isOpenForIngresso(?CarbonInterface $moment = null): bool
    {
        $m = $moment ? Carbon::instance($moment) : now();

        $open = $m->copy()->startOfDay()->setTime(7, 0, 0);
        $close = $m->copy()->startOfDay()->setTime(23, 59, 59);

        return $m->greaterThanOrEqualTo($open) && $m->lessThanOrEqualTo($close);
    }

    public static function label(): string
    {
        return '07:00 — 23:59';
    }

    public static function closedMessage(): string
    {
        return 'Fuera de horario de operación. El ingreso de vehículos está permitido de '.self::label().' (hora local).';
    }
}
