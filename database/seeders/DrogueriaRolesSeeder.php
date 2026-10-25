<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DrogueriaRolesSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'ver-productos', 'crear-productos', 'editar-productos', 'eliminar-productos',
            'ver-lotes', 'recibir-lotes',
            'ver-proveedores', 'crear-proveedores', 'editar-proveedores', 'eliminar-proveedores',
            'ver-compras', 'crear-compras', 'anular-compras',
            'ver-ventas', 'crear-ventas', 'anular-ventas', 'dispensar-control-especial',
            'ver-clientes', 'crear-clientes', 'editar-clientes', 'eliminar-clientes',
            'ver-reportes', 'ver-costos',
            'gestionar-usuarios', 'gestionar-roles',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Admin (dueño): todo, excepto recibir lotes (segregación: quien compra no recibe)
        // y autorizar control especial (responsabilidad técnica del regente)
        Role::findOrCreate('admin')->syncPermissions(
            array_diff($permissions, ['recibir-lotes', 'dispensar-control-especial'])
        );

        // Regente: recibe y revisa lotes y autoriza el control especial
        Role::findOrCreate('regente')->syncPermissions([
            'ver-productos', 'crear-productos', 'editar-productos',
            'ver-lotes', 'recibir-lotes',
            'ver-proveedores',
            'ver-compras',
            'ver-ventas', 'anular-ventas', 'dispensar-control-especial',
            'ver-clientes',
            'ver-reportes',
        ]);

        // Cajero: vende y cobra; no ve costos, compras ni lotes
        Role::findOrCreate('cajero')->syncPermissions([
            'ver-productos',
            'ver-ventas', 'crear-ventas',
            'ver-clientes', 'crear-clientes', 'editar-clientes',
        ]);

        // Auxiliar de bodega: descarga y registra lotes; no vende
        Role::findOrCreate('auxiliar-bodega')->syncPermissions([
            'ver-productos',
            'ver-lotes', 'recibir-lotes',
            'ver-compras',
        ]);
    }
}