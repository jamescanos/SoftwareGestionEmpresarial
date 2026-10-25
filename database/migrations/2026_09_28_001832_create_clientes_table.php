<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('documento', 20)->unique();
            $table->string('nombre', 150);
            $table->date('fecha_nacimiento');
            $table->string('nivel_vip', 20)->default('estandar');
            $table->decimal('saldo_fichas', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};