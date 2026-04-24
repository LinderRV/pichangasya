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
            $table->foreignId('cliente_id')
            ->constrained('clientes')
            ->restrictOnDelete()
            ->cascadeOnUpdate();

            $table->foreignId('cancha_id')
                ->constrained('canchas')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('estado_reserva_id')
                ->constrained('estado_reservas')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->date('fecha_reserva');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('observaciones', 200)->nullable();
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
