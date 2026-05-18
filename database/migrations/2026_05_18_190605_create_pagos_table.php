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
            $table->foreignId('id_reserva')->references('id')->on('reservas');
            $table->foreignId('id_metodo_pago')->references('id')->on('metodo_pagos');
            $table->string('codigo_operacion', 100)->unique();
            $table->decimal('monto', 10, 2)->nullable();
            $table->string('comprobante_url', 255)->nullable();
            $table->enum('estado', ['confirmado','reembolsado','anulado'])->default('confirmado');
            $table->dateTime('fecha_pago');
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
