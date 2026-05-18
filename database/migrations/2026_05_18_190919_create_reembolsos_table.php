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
        Schema::create('reembolsos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_reserva')->references('id')->on('reservas');
            $table->foreignId('id_pago')->references('id')->on('pagos');
            $table->foreignId('id_usuario')->references('id')->on('usuarios');
            $table->decimal('monto', 10, 2);
            $table->enum('metodo_reembolso', ['yape','plin','transferencia','efectivo','otro'])->default('yape');
            $table->string('codigo_operacion', 100)->nullable();
            $table->string('observacion', 255)->nullable();
            $table->dateTime('fecha_reembolso');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reembolsos');
    }
};
