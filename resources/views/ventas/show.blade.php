<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Venta {{ $venta->numero_factura }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4 border-t-4 border-amber-500">

                @if (session('success'))
                    <div class="p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                    <p><strong>Factura:</strong> {{ $venta->numero_factura }}</p>
                    <p><strong>Fecha:</strong> {{ $venta->fecha_venta->format('d/m/Y') }}</p>
                    <p><strong>Cliente:</strong> {{ $venta->cliente?->nombre }} ({{ $venta->cliente?->documento }})</p>
                    <p><strong>Cajero:</strong> {{ $venta->user?->name }}</p>
                    <p><strong>Estado:</strong> {{ ucfirst($venta->estado) }}</p>
                    @if ($venta->notas)
                        <p><strong>Notas:</strong> {{ $venta->notas }}</p>
                    @endif
                </div>

                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="p-2">Ficha</th>
                            <th class="p-2">Cantidad</th>
                            <th class="p-2">Valor unitario</th>
                            <th class="p-2">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($venta->detalles as $detalle)
                            <tr class="border-b">
                                <td class="p-2">{{ $detalle->ficha?->denominacion }}</td>
                                <td class="p-2">{{ $detalle->cantidad }}</td>
                                <td class="p-2">${{ number_format((float) $detalle->valor_unitario, 0, ',', '.') }}</td>
                                <td class="p-2">${{ number_format((float) $detalle->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="text-right space-y-1">
                    <p>Subtotal: <strong>${{ number_format((float) $venta->subtotal, 0, ',', '.') }}</strong></p>
                    <p>IVA (19%): <strong>${{ number_format((float) $venta->impuesto, 0, ',', '.') }}</strong></p>
                    <p class="text-lg">Total: <strong>${{ number_format((float) $venta->total, 0, ',', '.') }}</strong></p>
                </div>
                
                <a href="{{ route('ventas.pdf', $venta) }}" target="_blank" class="text-blue-700 underline me-4">Descargar PDF</a>
                <a href="{{ route('ventas.index') }}" class="underline">Volver al listado</a>
            </div>
        </div>
    </div>
</x-app-layout>