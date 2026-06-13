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
        Schema::create('complejo_deportivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_distrito')->references('id')->on('distritos');
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->string('ruc')->nullable()->unique();
            $table->string('correo')->unique();
            $table->string('direccion')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('imagen')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complejo_deportivos');
    }
};
