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
        Schema::create('caja_movimientos', function (Blueprint $table) {
            $table->id();
            // Relación con el usuario (quién abre/cierra la caja)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Montos de dinero
            $table->decimal('monto_apertura', 10, 2);
            $table->decimal('monto_cierre', 10, 2)->nullable(); // Será null hasta que se cierre

            // Fechas de control
            $table->timestamp('fecha_apertura')->useCurrent();
            $table->timestamp('fecha_cierre')->nullable();

            // Estado del turno de la caja
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }
};
