<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Compras de fichas</h2>
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
                    @can('crear-compras')
                        <a href="{{ route('compras.create') }}" class="text-amber-700 underline">+ Nueva orden de compra</a>
                    @endcan
                    <div class="text-sm text-gray-700 space-x-4">
                        <span><strong>Por recibir:</strong> {{ $pendientes }}</span>
                        <span><strong>Total del mes:</strong> ${{ number_format((float) $totalMes, 0, ',', '.') }}</span>
                    </div>
                </div>

                <form method="GET" action="{{ route('compras.index') }}" class="flex flex-wrap gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por orden, factura o proveedor..." class="border-gray-300 rounded-md">
                    <select name="estado" class="border-gray-300 rounded-md">
                        <option value="">Todos los estados</option>
                        @foreach (['pendiente', 'parcial', 'recibida', 'anulada'] as $estado)
                            <option value="{{ $estado }}" @selected(request('estado') === $estado)>{{ ucfirst($estado) }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="from" value="{{ request('from') }}" class="border-gray-300 rounded-md">
                    <input type="date" name="to" value="{{ request('to') }}" class="border-gray-300 rounded-md">
                    <x-primary-button>Filtrar</x-primary-button>
                </form>

                @php
                    $colores = [
                        'pendiente' => 'bg-amber-100 text-amber-800',
                        'parcial' => 'bg-blue-100 text-blue-800',
                        'recibida' => 'bg-green-100 text-green-800',
                        'anulada' => 'bg-red-100 text-red-800',
                    ];
                @endphp

                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="p-2">Orden</th>
                            <th class="p-2">Fecha</th>
                            <th class="p-2">Proveedor</th>
                            <th class="p-2">Total</th>
                            <th class="p-2">Estado</th>
                            <th class="p-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($compras as $compra)
                            <tr class="border-b">
                                <td class="p-2 font-medium">{{ $compra->numero_compra }}</td>
                                <td class="p-2">{{ $compra->fecha_compra->format('d/m/Y') }}</td>
                                <td class="p-2">{{ $compra->proveedor?->nombre }}</td>
                                <td class="p-2">${{ number_format((float) $compra->total, 0, ',', '.') }}</td>
                                <td class="p-2">
                                    <span class="px-2 py-1 rounded-full text-xs {{ $colores[$compra->estado] ?? '' }}">{{ ucfirst($compra->estado) }}</span>
                                </td>
                                <td class="p-2 space-x-2">
                                    <a href="{{ route('compras.show', $compra) }}" class="text-blue-700 underline">Ver</a>
                                    <a href="{{ route('compras.pdf', $compra) }}" target="_blank" class="text-blue-700 underline">PDF</a>
                                    @can('eliminar-compras')
                                        @if ($compra->puedeAnularse())
                                            <form method="POST" action="{{ route('compras.destroy', $compra) }}" class="inline" onsubmit="return confirm('¿Anular esta orden de compra?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-700 underline">Anular</button>
                                            </form>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 text-center text-gray-500">No hay compras para mostrar.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $compras->links() }}
            </div>
        </div>
    </div>
</x-app-layout>