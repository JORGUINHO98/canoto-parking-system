<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;

class CanotoParkingDemoSeeder extends Seeder
{
    public function run(): void
    {
        $visitante = Cliente::query()->create([
            'nombre' => 'María Visitante Demo',
            'tipo_cliente' => Cliente::TIPO_VISITANTE,
            'fecha_proximo_pago' => null,
        ]);

        Vehiculo::query()->create([
            'cliente_id' => $visitante->id,
            'placa' => 'VISI1234',
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'color' => 'Gris',
        ]);

        $abonado = Cliente::query()->create([
            'nombre' => 'Carlos Abonado Demo',
            'tipo_cliente' => Cliente::TIPO_ABONADO,
            'fecha_proximo_pago' => now()->addMonth()->toDateString(),
        ]);

        Vehiculo::query()->create([
            'cliente_id' => $abonado->id,
            'placa' => 'ABON5678',
            'marca' => 'Suzuki',
            'modelo' => 'Swift',
            'color' => 'Azul',
        ]);

        $vip = Cliente::query()->create([
            'nombre' => 'Laura VIP Demo',
            'tipo_cliente' => Cliente::TIPO_ABONADO_VIP,
            'fecha_proximo_pago' => now()->addMonth()->toDateString(),
        ]);

        Vehiculo::query()->create([
            'cliente_id' => $vip->id,
            'placa' => 'VIPV9999',
            'marca' => 'BMW',
            'modelo' => 'X3',
            'color' => 'Negro',
        ]);
    }
}
