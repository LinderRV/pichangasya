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
            $table->string('nombre', 120);
            $table->string('direccion', 200);
            $table->string('distrito', 100)->nullable();
            $table->string('telefono_contacto', 20)->nullable();
            $table->string('correo_contacto', 120)->nullable();
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
