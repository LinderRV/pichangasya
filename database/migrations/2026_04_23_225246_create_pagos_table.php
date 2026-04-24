<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')
            ->constrained('reservas')
            ->restrictOnDelete()
            ->cascadeOnUpdate();

            $table->foreignId('metodo_pago_id')
                ->constrained('metodo_pagos')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->decimal('monto', 10, 2);
            $table->enum('estado_pago', ['pendiente', 'pagado', 'rechazado'])->default('pendiente');
            $table->string('codigo_operacion', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
