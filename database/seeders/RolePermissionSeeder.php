<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'ver-juegos', 'crear-juegos', 'editar-juegos', 'eliminar-juegos',
            'ver-mesas', 'crear-mesas', 'editar-mesas', 'eliminar-mesas',
            'ver-clientes', 'crear-clientes', 'editar-clientes', 'eliminar-clientes',
            'ver-fichas',
            'ver-ventas', 'crear-ventas', 'editar-ventas', 'eliminar-ventas',
            // Clase 10
            'ver-proveedores', 'crear-proveedores', 'editar-proveedores', 'eliminar-proveedores',
            'ver-compras', 'crear-compras', 'editar-compras', 'eliminar-compras', 'recibir-compras',
            'ver-reportes',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        Role::findOrCreate('admin')->syncPermissions(Permission::all());

        // Cajero: atiende clientes, vende fichas y recibe las fichas que llegan del proveedor
        Role::findOrCreate('cajero')->syncPermissions([
            'ver-clientes', 'crear-clientes', 'editar-clientes',
            'ver-mesas', 'ver-juegos',
            'ver-fichas',
            'ver-ventas', 'crear-ventas',
            'ver-proveedores', 'ver-compras', 'recibir-compras',
        ]);

        // Crupier: maneja mesas y juegos
        Role::findOrCreate('crupier')->syncPermissions([
            'ver-juegos', 'ver-mesas', 'editar-mesas',
        ]);
    }
}