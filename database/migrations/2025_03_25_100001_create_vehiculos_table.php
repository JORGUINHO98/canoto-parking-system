<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('placa', 16)->unique();
            $table->string('color', 64)->nullable();
            $table->string('modelo', 120)->nullable();
            $table->string('marca', 120)->nullable();
            $table->timestamps();

            $table->index('placa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
