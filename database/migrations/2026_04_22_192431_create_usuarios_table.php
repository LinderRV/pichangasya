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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')
                ->constrained('roles')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('correo', 120)->unique();
            $table->string('contrasena', 255);
            $table->string('telefono', 20)->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
