<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();  // Ej: 'Visualizar valor del inventario'
            $table->string('slug')->unique();  // Ej: 'view-inventory-value'
            $table->string('module');          // Ej: 'INVENTARIO', 'CATÁLOGO'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
