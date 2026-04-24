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
        Schema::create('horario_configurados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cancha_id')
            ->constrained('canchas')
            ->restrictOnDelete()
            ->cascadeOnUpdate();

            $table->enum('dia_semana', [
                'lunes',
                'martes',
                'miercoles',
                'jueves',
                'viernes',
                'sabado',
                'domingo'
            ]);

            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horario_configurados');
    }
};
