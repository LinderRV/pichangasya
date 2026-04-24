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
        Schema::create('reserva_servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')
            ->constrained('reservas')
            ->restrictOnDelete()
            ->cascadeOnUpdate();

            $table->foreignId('servicio_id')
                ->constrained('servicio_adicionals')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedInteger('cantidad')->default(1);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('importe', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserva_servicios');
    }
};
