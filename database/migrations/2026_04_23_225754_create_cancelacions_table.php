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
        Schema::create('cancelacions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')
            ->unique()
            ->constrained('reservas')
            ->restrictOnDelete()
            ->cascadeOnUpdate();

            $table->string('motivo', 200);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancelacions');
    }
};
