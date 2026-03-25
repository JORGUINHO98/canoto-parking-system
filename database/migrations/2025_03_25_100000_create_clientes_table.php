<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('tipo_cliente', 32)->default('visitante');
            $table->date('fecha_proximo_pago')->nullable();
            $table->string('telefono', 32)->nullable();
            $table->timestamps();

            $table->index('tipo_cliente');
            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
