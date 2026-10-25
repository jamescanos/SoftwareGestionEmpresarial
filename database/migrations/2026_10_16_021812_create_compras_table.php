<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->string('numero_compra', 20)->unique(); // OC-00001
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->date('fecha_compra');
            $table->string('factura_proveedor', 50)->nullable();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('impuesto', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->enum('estado', ['pendiente', 'parcial', 'recibida', 'anulada'])->default('pendiente');
            $table->timestamp('recibida_en')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};