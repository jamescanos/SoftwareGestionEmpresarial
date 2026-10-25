<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            ['tipo_documento' => 'NIT', 'numero_documento' => '900111222', 'nombre' => 'Fichas y Naipes del Pacífico S.A.S.', 'contacto' => 'Laura Mejía', 'email' => 'ventas@fichaspacifico.test', 'telefono' => '3001112233', 'direccion' => 'Calle 20 #15-30'],
            ['tipo_documento' => 'NIT', 'numero_documento' => '900333444', 'nombre' => 'Casino Supplies Colombia S.A.S.', 'contacto' => 'Andrés Londoño', 'email' => 'contacto@casinosupplies.test', 'telefono' => '3104445566', 'direccion' => 'Carrera 7 #60-12'],
            ['tipo_documento' => 'NIT', 'numero_documento' => '900555666', 'nombre' => 'Distribuidora de Juegos Andina', 'contacto' => 'Marta Ríos', 'email' => 'andina@juegos.test', 'telefono' => '3207778899', 'direccion' => 'Av. Principal #8-45'],
        ];

        // firstOrCreate: si el documento ya existe no se duplica
        foreach ($proveedores as $proveedor) {
            Proveedor::firstOrCreate(['numero_documento' => $proveedor['numero_documento']], $proveedor + ['activo' => true]);
        }
    }
}