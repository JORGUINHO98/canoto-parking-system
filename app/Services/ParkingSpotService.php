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
        $pisosDisponibles = [];

        // 1. Determinar los pisos permitidos según el tipo de cliente
        if ($tipoEfectivo === Cliente::TIPO_ABONADO_VIP) {
            // Los VIP tienen prioridad desde el Piso 1
            $pisosDisponibles = [1, 2, 3, 4, 5];
        } elseif (in_array($tipoEfectivo, [Cliente::TIPO_ABONADO, Cliente::TIPO_VISITANTE], true)) {
            // Visitantes y Abonados van directo a partir del Piso 2
            $pisosDisponibles = [2, 3, 4, 5];
        } else {
            return null; 
        }

        // 2. Consultar a la base de datos todos los vehículos parqueados AHORA MISMO
        // Buscamos ingresos que NO tengan fecha de salida registrada
        $ingresosActivos = Ingreso::whereNull('salida_at')->get(['piso', 'espacio']);
        
        // Creamos una lista rápida de los espacios ocupados agrupados por piso
        $ocupados = [];
        foreach ($ingresosActivos as $ingreso) {
            $ocupados[$ingreso->piso][] = $ingreso->espacio;
        }

        // 3. Buscar el primer espacio libre real
        foreach ($pisosDisponibles as $piso) {
            // Revisamos cada nicho del 1 al 20 en este piso
            for ($espacio = 1; $espacio <= self::ESPACIOS_POR_PISO; $espacio++) {
                
                // Si el piso está completamente vacío O si este espacio específico no está ocupado
                if (!isset($ocupados[$piso]) || !in_array($espacio, $ocupados[$piso], true)) {
                    // ¡Encontramos un espacio libre! Lo asignamos y terminamos la búsqueda
                    return [
                        'piso' => $piso,
                        'espacio' => $espacio
                    ];
                }
            }
        }

        // Si el código llega hasta aquí, significa que los 5 pisos (100 espacios) están llenos
        return null; 
    }
}