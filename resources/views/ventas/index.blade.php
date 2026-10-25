<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ventas de fichas</h2>
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

                <div class="flex flex-wrap justify-between gap-4 items-center">
                    @can('crear-ventas')
                        <a href="{{ route('ventas.create') }}" class="text-amber-700 underline">+ Nueva venta</a>
                    @endcan
                    <div class="text-sm text-gray-700">
                        <strong>Total del mes:</strong> ${{ number_format((float) $totalMes, 0, ',', '.') }}
                    </div>
                </div>

                <form method="GET" action="{{ route('ventas.index') }}" class="flex flex-wrap gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por factura o cliente..." class="border-gray-300 rounded-md">
                    <input type="date" name="from" value="{{ request('from') }}" class="border-gray-300 rounded-md">
                    <input type="date" name="to" value="{{ request('to') }}" class="border-gray-300 rounded-md">
                    <x-primary-button>Filtrar</x-primary-button>
                </form>

                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="p-2">Factura</th>
                            <th class="p-2">Fecha</th>
                            <th class="p-2">Cliente</th>
                            <th class="p-2">Cajero</th>
                            <th class="p-2">Total</th>
                            <th class="p-2">Estado</th>
                            <th class="p-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ventas as $venta)
                            <tr class="border-b">
                                <td class="p-2 font-medium">{{ $venta->numero_factura }}</td>
                                <td class="p-2">{{ $venta->fecha_venta->format('d/m/Y') }}</td>
                                <td class="p-2">{{ $venta->cliente?->nombre }}</td>
                                <td class="p-2">{{ $venta->user?->name }}</td>
                                <td class="p-2">${{ number_format((float) $venta->total, 0, ',', '.') }}</td>
                                <td class="p-2">{{ ucfirst($venta->estado) }}</td>
                                <td class="p-2 space-x-2">
                                    <a href="{{ route('ventas.show', $venta) }}" class="text-blue-700 underline">Ver</a>
                                    <a href="{{ route('ventas.pdf', $venta) }}" target="_blank" class="text-blue-700 underline">PDF</a>
                                    @can('eliminar-ventas')
                                        <form method="POST" action="{{ route('ventas.destroy', $venta) }}" class="inline" onsubmit="return confirm('¿Anular esta venta? Las fichas volverán al stock.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-700 underline">Anular</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-4 text-center text-gray-500">No hay ventas para mostrar.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $ventas->links() }}
            </div>
        </div>
    </div>
</x-app-layout>