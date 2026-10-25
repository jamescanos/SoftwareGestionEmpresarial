<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Juegos {{ $showTrashed ? '(papelera)' : '' }}
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
                    @can('crear-juegos')
                        <a href="{{ route('juegos.create') }}" class="text-amber-700 underline">+ Nuevo juego</a>
                    @endcan
                    @can('eliminar-juegos')
                        @if ($showTrashed)
                            <a href="{{ route('juegos.index') }}" class="text-amber-700 underline">Ver juegos</a>
                        @else
                            <a href="{{ route('juegos.index', ['trashed' => 1]) }}" class="text-amber-700 underline">Ver papelera ({{ $trashedCount }})</a>
                        @endif
                    @endcan
                </div>

                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="p-2">Nombre</th>
                            <th class="p-2">Apuesta mín.</th>
                            <th class="p-2">Apuesta máx.</th>
                            <th class="p-2">Mesas</th>
                            <th class="p-2">Estado</th>
                            <th class="p-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($juegos as $juego)
                            <tr class="border-b">
                                <td class="p-2 font-medium">{{ $juego->nombre }}</td>
                                <td class="p-2">${{ number_format($juego->apuesta_min, 0, ',', '.') }}</td>
                                <td class="p-2">${{ number_format($juego->apuesta_max, 0, ',', '.') }}</td>
                                <td class="p-2">{{ $juego->mesas_count }}</td>
                                <td class="p-2">{{ $juego->activo ? 'Activo' : 'Inactivo' }}</td>
                                <td class="p-2 space-x-2">
                                    @if ($juego->trashed())
                                        <form method="POST" action="{{ route('juegos.restore', $juego) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-green-700 underline">Restaurar</button>
                                        </form>
                                    @else
                                        @can('editar-juegos')
                                            <a href="{{ route('juegos.edit', $juego) }}" class="text-blue-700 underline">Editar</a>
                                        @endcan
                                        @can('eliminar-juegos')
                                            <form method="POST" action="{{ route('juegos.destroy', $juego) }}" class="inline" onsubmit="return confirm('¿Enviar este juego a la papelera?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-700 underline">Eliminar</button>
                                            </form>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 text-center text-gray-500">No hay juegos para mostrar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $juegos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>