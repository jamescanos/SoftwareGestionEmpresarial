<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Juego;

class JuegoSeeder extends Seeder
{
    public function run(): void
    {
        Juego::create(['nombre' => 'Blackjack', 'apuesta_min' => 10000, 'apuesta_max' => 1000000]);
        Juego::create(['nombre' => 'Póker', 'apuesta_min' => 20000, 'apuesta_max' => 2000000]);
        Juego::create(['nombre' => 'Tragamonedas', 'apuesta_min' => 1000, 'apuesta_max' => 100000]);
        Juego::create(['nombre' => 'Bacará', 'apuesta_min' => 15000, 'apuesta_max' => 1500000]);
    }
}