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
        Schema::create('usuario_complejos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->references('id')->on('usuarios');
            $table->foreignId('id_complejo')->references('id')->on('complejo_deportivos');
            $table->enum('cargo', ['Dueño', 'Empleado'])->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');

            // Un usuario no se repite en el MISMO complejo, pero sí puede estar en varios
            $table->unique(['id_usuario', 'id_complejo']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario_complejos');
    }
};
