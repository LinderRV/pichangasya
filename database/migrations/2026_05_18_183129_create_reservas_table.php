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
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_reserva',30)->unique();
            $table->foreignId('id_cliente')->references('id')->on('clientes');
            $table->foreignId('id_cancha')->references('id')->on('canchas');
            $table->foreignId('id_estado_reserva')->references('id')->on('estado_reservas');
            $table->date('fecha_reserva');
            $table->time('hora_inicio');
            $table->time('hora_fin');

            $table->decimal('precio_hora', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total', 10, 2);

            $table->dateTime('confirmado_at');

            $table->dateTime('cancelado_at')->nullable();

           $table->foreignId('id_usuario_cancelado')
            ->nullable()
            ->constrained('usuarios');
            $table->string('motivo_cancelacion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
