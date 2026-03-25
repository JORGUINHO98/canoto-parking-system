<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->unsignedTinyInteger('piso');
            $table->timestamp('entrada_at')->useCurrent();
            $table->timestamp('salida_at')->nullable();
            $table->string('tipo_registrado', 32);
            $table->string('tipo_efectivo', 32);
            $table->boolean('abono_vencido_tratado_como_visitante')->default(false);
            $table->decimal('total_bs', 10, 2)->nullable();
            $table->timestamps();

            $table->index(['vehiculo_id', 'salida_at']);
            $table->index('entrada_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingresos');
    }
};
