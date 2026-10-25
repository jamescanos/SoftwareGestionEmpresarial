<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        Cliente::create(['documento' => '1001234567', 'nombre' => 'Carlos Ramírez', 'fecha_nacimiento' => '1985-04-12', 'nivel_vip' => 'oro', 'saldo_fichas' => 250000]);
        Cliente::create(['documento' => '1002345678', 'nombre' => 'María Gómez', 'fecha_nacimiento' => '1990-11-03', 'nivel_vip' => 'estandar', 'saldo_fichas' => 50000]);
        Cliente::create(['documento' => '1003456789', 'nombre' => 'Andrés Torres', 'fecha_nacimiento' => '1978-07-22', 'nivel_vip' => 'platino', 'saldo_fichas' => 1000000]);
        Cliente::create(['documento' => '1004567890', 'nombre' => 'Laura Sánchez', 'fecha_nacimiento' => '1995-01-30', 'nivel_vip' => 'estandar', 'saldo_fichas' => 30000]);
        Cliente::create(['documento' => '1005678901', 'nombre' => 'Jorge Pérez', 'fecha_nacimiento' => '1982-09-15', 'nivel_vip' => 'oro', 'saldo_fichas' => 180000]);
    }
}