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
        Schema::create('historial_estado_reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_reserva')->references('id')->on('reservas');
            $table->foreignId('id_estado_reserva')->references('id')->on('estado_reservas');
            $table->foreignId('id_usuario')->constrained('usuarios');
            $table->dateTime('fecha_cambio');
            $table->string('observacion')->nullable();          
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_estado_reservas');
    }
};
