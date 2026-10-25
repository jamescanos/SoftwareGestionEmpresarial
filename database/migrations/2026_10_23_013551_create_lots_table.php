<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->string('lot_number', 50);
            $table->date('expiration_date');
            $table->unsignedInteger('quantity');
            $table->timestamps();

            // El mismo número de lote no se repite dentro de un producto
            $table->unique(['product_id', 'lot_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};