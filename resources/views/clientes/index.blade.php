<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Clientes {{ $showTrashed ? '(papelera)' : '' }}
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
                    @can('crear-clientes')
                        <a href="{{ route('clientes.create') }}" class="text-amber-700 underline">+ Nuevo cliente</a>
                    @endcan
                    @can('eliminar-clientes')
                        @if ($showTrashed)
                            <a href="{{ route('clientes.index') }}" class="text-amber-700 underline">Ver clientes</a>
                        @else
                            <a href="{{ route('clientes.index', ['trashed' => 1]) }}" class="text-amber-700 underline">Ver papelera ({{ $trashedCount }})</a>
                        @endif
                    @endcan
                </div>

                <form method="GET" action="{{ route('clientes.index') }}" class="flex flex-wrap gap-2">
                    @if ($showTrashed)
                        <input type="hidden" name="trashed" value="1">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o documento..." class="border-gray-300 rounded-md">
                    <select name="nivel" class="border-gray-300 rounded-md">
                        <option value="">Todos los niveles</option>
                        <option value="estandar" @selected(request('nivel') === 'estandar')>Estándar</option>
                        <option value="oro" @selected(request('nivel') === 'oro')>Oro</option>
                        <option value="platino" @selected(request('nivel') === 'platino')>Platino</option>
                    </select>
                    <x-primary-button>Filtrar</x-primary-button>
                </form>

                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="p-2">Documento</th>
                            <th class="p-2">Nombre</th>
                            <th class="p-2">Nivel VIP</th>
                            <th class="p-2">Saldo de fichas</th><th class="p-2">Ventas</th>
                            <th class="p-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clientes as $cliente)
                            <tr class="border-b">
                                <td class="p-2">{{ $cliente->documento }}</td>
                                <td class="p-2 font-medium">{{ $cliente->nombre }}</td>
                                <td class="p-2">{{ ucfirst($cliente->nivel_vip) }}</td>
                                <td class="p-2">${{ number_format((float) $cliente->saldo_fichas, 0, ',', '.') }}</td><td class="p-2">{{ $cliente->ventas_count }}</td>
                                <td class="p-2 space-x-2">
                                    @if ($cliente->trashed())
                                        <form method="POST" action="{{ route('clientes.restore', $cliente) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-green-700 underline">Restaurar</button>
                                        </form>
                                    @else
                                        @can('editar-clientes')
                                            <a href="{{ route('clientes.edit', $cliente) }}" class="text-blue-700 underline">Editar</a>
                                        @endcan
                                        @can('eliminar-clientes')
                                            <form method="POST" action="{{ route('clientes.destroy', $cliente) }}" class="inline" onsubmit="return confirm('¿Enviar este cliente a la papelera?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-700 underline">Eliminar</button>
                                            </form>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 text-center text-gray-500">No hay clientes para mostrar.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $clientes->links() }}
            </div>
        </div>
    </div>
</x-app-layout>