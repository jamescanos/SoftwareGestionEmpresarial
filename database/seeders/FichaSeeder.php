<?php

namespace Database\Seeders;

use App\Models\Ficha;
use Illuminate\Database\Seeder;

class FichaSeeder extends Seeder
{
    public function run(): void
    {
        $fichas = [
            ['denominacion' => 'Ficha $1.000',   'valor' => 1000,   'stock' => 500],
            ['denominacion' => 'Ficha $5.000',   'valor' => 5000,   'stock' => 400],
            ['denominacion' => 'Ficha $10.000',  'valor' => 10000,  'stock' => 300],
            ['denominacion' => 'Ficha $50.000',  'valor' => 50000,  'stock' => 200],
            // Stock bajo a propósito: sirve para probar el error de "stock insuficiente"
            ['denominacion' => 'Ficha $100.000', 'valor' => 100000, 'stock' => 4],
        ];

        // firstOrCreate: si la ficha ya existe no la duplica (se puede ejecutar varias veces)
        foreach ($fichas as $ficha) {
            Ficha::firstOrCreate(['valor' => $ficha['valor']], $ficha + ['activo' => true]);
        }
    }
}