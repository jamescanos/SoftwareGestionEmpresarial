<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mesas {{ $showTrashed ? '(papelera)' : '' }}
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
                    @can('crear-mesas')
                        <a href="{{ route('mesas.create') }}" class="text-amber-700 underline">+ Nueva mesa</a>
                    @endcan
                    @can('eliminar-mesas')
                        @if ($showTrashed)
                            <a href="{{ route('mesas.index') }}" class="text-amber-700 underline">Ver mesas</a>
                        @else
                            <a href="{{ route('mesas.index', ['trashed' => 1]) }}" class="text-amber-700 underline">Ver papelera ({{ $trashedCount }})</a>
                        @endif
                    @endcan
                </div>

                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="p-2">ID</th>
                            <th class="p-2">Juego</th>
                            <th class="p-2">Apuesta mín.</th>
                            <th class="p-2">Apuesta máx.</th>
                            <th class="p-2">Estado</th>
                            <th class="p-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mesas as $mesa)
                            <tr class="border-b">
                                <td class="p-2">{{ $mesa->id }}</td>
                                <td class="p-2 font-medium">{{ $mesa->juego->nombre }}</td>
                                <td class="p-2">${{ number_format($mesa->juego->apuesta_min, 0, ',', '.') }}</td>
                                <td class="p-2">${{ number_format($mesa->juego->apuesta_max, 0, ',', '.') }}</td>
                                <td class="p-2">
                                    <span class="px-2 py-1 rounded-full text-xs {{ $mesa->estado === 'abierta' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ ucfirst($mesa->estado) }}
                                    </span>
                                </td>
                                <td class="p-2 space-x-2">
                                    @if ($mesa->trashed())
                                        <form method="POST" action="{{ route('mesas.restore', $mesa) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-green-700 underline">Restaurar</button>
                                        </form>
                                    @else
                                        @can('editar-mesas')
                                            <a href="{{ route('mesas.edit', $mesa) }}" class="text-blue-700 underline">Editar</a>
                                        @endcan
                                        @can('eliminar-mesas')
                                            <form method="POST" action="{{ route('mesas.destroy', $mesa) }}" class="inline" onsubmit="return confirm('¿Enviar esta mesa a la papelera?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-700 underline">Eliminar</button>
                                            </form>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 text-center text-gray-500">No hay mesas para mostrar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $mesas->links() }}
            </div>
        </div>
    </div>
</x-app-layout>