<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Ingreso;

class ParkingSpotService
{
    /**
     * Definición de la capacidad del parqueo.
     */
    public const PISOS_TOTALES = 5;
    public const ESPACIOS_POR_PISO = 20;

    /**
     * Calcula el primer espacio disponible basándose en el tipo de cliente
     * y los vehículos que se encuentran actualmente en el parqueo.
     * * @param string $tipoEfectivo Tipo de cliente (VIP, Abonado, Visitante)
     * @return array|null ['piso' => int, 'espacio' => int] o null si está lleno.
     */
    public function calcularEspacioDisponible(string $tipoEfectivo): ?array
    {
        // 1. Obtener todos los vehículos que están dentro del parqueo actualmente.
        // Filtramos por salida_at NULL para ver solo los espacios ocupados "ahora mismo".
        $registrosActivos = Ingreso::query()
            ->whereNull('salida_at')
            ->whereNotNull('espacio')
            ->whereNotNull('piso')
            ->get(['piso', 'espacio']);

        /**
         * 2. Creamos una matriz de ocupación real.
         * Forzamos el valor a (int) porque a veces la base de datos devuelve 
         * los números como strings (ej. "1") y eso rompe las comparaciones.
         */
        $mapaOcupacion = [];
        foreach ($registrosActivos as $registro) {
            $p = (int) $registro->piso;
            $e = (int) $registro->espacio;

            if (!isset($mapaOcupacion[$p])) {
                $mapaOcupacion[$p] = [];
            }
            $mapaOcupacion[$p][] = $e;
        }

        /**
         * 3. Definir el orden de búsqueda según el privilegio.
         * VIP: Empieza buscando en Piso 1. Si se llena, busca en los demás.
         * Otros: Solo buscan del Piso 2 al 5.
         */
        if ($tipoEfectivo === Cliente::TIPO_ABONADO_VIP) {
            $pisosPrioridad = [1, 2, 3, 4, 5];
        } else {
            $pisosPrioridad = [2, 3, 4, 5];
        }

        /**
         * 4. Algoritmo de búsqueda del primer hueco libre.
         * Recorremos los pisos en orden de prioridad y luego los espacios del 1 al 20.
         */
        foreach ($pisosPrioridad as $pisoActual) {
            $espaciosOcupadosEnEstePiso = $mapaOcupacion[$pisoActual] ?? [];

            for ($nroEspacio = 1; $nroEspacio <= self::ESPACIOS_POR_PISO; $nroEspacio++) {
                // Si el número de espacio no está en la lista de ocupados del piso actual...
                if (!in_array($nroEspacio, $espaciosOcupadosEnEstePiso)) {
                    // ¡Encontramos el primer lugar libre!
                    return [
                        'piso' => $pisoActual,
                        'espacio' => $nroEspacio
                    ];
                }
            }
        }

        // Si el código llega aquí, significa que no hay espacios en ningún piso permitido.
        return null;
    }
}