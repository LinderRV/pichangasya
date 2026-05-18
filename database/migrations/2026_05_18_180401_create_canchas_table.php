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
            $table->foreignId('id_complejo')->references('id')->on('complejo_deportivos');
            $table->foreignId('id_tipo_cancha')->references('id')->on('tipo_canchas');
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();  
            $table->decimal('precio_hora', 10, 2);   
            $table->integer('capacidad')->nullable();
            $table->string('foto')->nullable();
            $table->enum('estado', ['activo', 'inactivo','mantenimiento'])->default('activo');
            $table->timestamps();
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
