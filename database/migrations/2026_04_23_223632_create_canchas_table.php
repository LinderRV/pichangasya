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
        Schema::create('canchas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complejo_id')
            ->constrained('complejo_deportivos')
            ->restrictOnDelete()
            ->cascadeOnUpdate();

            $table->foreignId('tipo_cancha_id')
                ->constrained('tipo_canchas')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->string('nombre', 100);
            $table->unsignedInteger('capacidad')->nullable();
            $table->decimal('precio_hora', 10, 2);
            $table->enum('estado', ['disponible', 'mantenimiento', 'inactiva'])->default('disponible');
            $table->string('descripcion', 200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canchas');
    }
};
