<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Ingreso;

class ParkingSpotService
{
    public const PISOS_TOTALES = 5;
    public const ESPACIOS_POR_PISO = 20;

    public function calcularEspacioDisponible(string $tipoEfectivo): ?array
    {
        // Obtener la matriz de espacios ocupados actualmente por piso
        // Solo cuenta los vehículos que aún están en el parqueo (salida_at is null)
        $ocupados = Ingreso::query()
            ->whereNull('salida_at')
            ->whereNotNull('espacio')
            ->get(['piso', 'espacio'])
            ->groupBy('piso')
            ->map(fn ($items) => $items->pluck('espacio')->toArray())
            ->toArray();

        // Definir orden de búsqueda de pisos dependiendo del privilegio
        $pisosPrioridad = $tipoEfectivo === Cliente::TIPO_ABONADO_VIP 
            ? [1, 2, 3, 4, 5] 
            : [2, 3, 4, 5];

        foreach ($pisosPrioridad as $piso) {
            $espaciosEnPiso = $ocupados[$piso] ?? [];

            // Buscar el primer espacio del 1 al 20 que esté libre
            for ($espacio = 1; $espacio <= self::ESPACIOS_POR_PISO; $espacio++) {
                if (! in_array($espacio, $espaciosEnPiso, true)) {
                    return ['piso' => $piso, 'espacio' => $espacio];
                }
            }
        }

        return null; // El parqueo está lleno para este perfil de cliente
    }
}