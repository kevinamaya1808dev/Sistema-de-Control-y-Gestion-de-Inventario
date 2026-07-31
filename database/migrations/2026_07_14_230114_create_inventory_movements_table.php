<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('inventory_movements', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->nullable();
        $table->foreignId('user_id');
        $table->foreignId('caja_id')->nullable();
        $table->string('type');
        $table->integer('quantity');
        $table->string('reason');
        $table->decimal('unit_price', 10, 2)->default(0);
        $table->decimal('total', 10, 2)->default(0);
        $table->decimal('monto_recibido', 10, 2)->default(0);
        $table->decimal('cambio', 10, 2)->default(0);
        $table->string('payment_method')->default('cash');
        
        $table->string('reference')->nullable(); 

        $table->timestamp('date')->useCurrent();
        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};