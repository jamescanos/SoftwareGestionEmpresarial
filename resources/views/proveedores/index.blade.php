<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Proveedores {{ $showTrashed ? '(papelera)' : '' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4 border-t-4 border-amber-500">

                @if (session('success'))
                    <div class="p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
                @endif

                <div class="flex flex-wrap gap-4">
                    @can('crear-proveedores')
                        <a href="{{ route('proveedores.create') }}" class="text-amber-700 underline">+ Nuevo proveedor</a>
                    @endcan
                    @can('eliminar-proveedores')
                        @if ($showTrashed)
                            <a href="{{ route('proveedores.index') }}" class="text-amber-700 underline">Ver proveedores</a>
                        @else
                            <a href="{{ route('proveedores.index', ['trashed' => 1]) }}" class="text-amber-700 underline">Ver papelera ({{ $trashedCount }})</a>
                        @endif
                    @endcan
                </div>

                <form method="GET" action="{{ route('proveedores.index') }}" class="flex flex-wrap gap-2">
                    @if ($showTrashed)
                        <input type="hidden" name="trashed" value="1">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, documento o contacto..." class="border-gray-300 rounded-md">
                    <select name="estado" class="border-gray-300 rounded-md">
                        <option value="">Todos los estados</option>
                        <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                        <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
                    </select>
                    <x-primary-button>Filtrar</x-primary-button>
                </form>

                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="p-2">Documento</th>
                            <th class="p-2">Nombre</th>
                            <th class="p-2">Contacto</th>
                            <th class="p-2">Teléfono</th>
                            <th class="p-2">Compras</th>
                            <th class="p-2">Estado</th>
                            <th class="p-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($proveedores as $proveedor)
                            <tr class="border-b">
                                <td class="p-2">{{ $proveedor->tipo_documento }} {{ $proveedor->numero_documento }}</td>
                                <td class="p-2 font-medium">{{ $proveedor->nombre }}</td>
                                <td class="p-2">{{ $proveedor->contacto }}</td>
                                <td class="p-2">{{ $proveedor->telefono }}</td>
                                <td class="p-2">{{ $proveedor->compras_count }}</td>
                                <td class="p-2">{{ $proveedor->activo ? 'Activo' : 'Inactivo' }}</td>
                                <td class="p-2 space-x-2">
                                    @if ($proveedor->trashed())
                                        <form method="POST" action="{{ route('proveedores.restore', $proveedor) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-green-700 underline">Restaurar</button>
                                        </form>
                                    @else
                                        @can('editar-proveedores')
                                            <a href="{{ route('proveedores.edit', $proveedor) }}" class="text-blue-700 underline">Editar</a>
                                        @endcan
                                        @can('eliminar-proveedores')
                                            <form method="POST" action="{{ route('proveedores.destroy', $proveedor) }}" class="inline" onsubmit="return confirm('¿Enviar este proveedor a la papelera?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-700 underline">Eliminar</button>
                                            </form>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-4 text-center text-gray-500">No hay proveedores para mostrar.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $proveedores->links() }}
            </div>
        </div>
    </div>
</x-app-layout>