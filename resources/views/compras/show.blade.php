<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Orden de compra {{ $compra->numero_compra }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4 border-t-4 border-amber-500">

                @if (session('success'))
                    <div class="p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="p-3 rounded bg-red-100 text-red-800">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                    <p><strong>Orden:</strong> {{ $compra->numero_compra }}</p>
                    <p><strong>Fecha:</strong> {{ $compra->fecha_compra->format('d/m/Y') }}</p>
                    <p><strong>Proveedor:</strong> {{ $compra->proveedor?->nombre }}</p>
                    <p><strong>Registró:</strong> {{ $compra->user?->name }}</p>
                    <p><strong>Estado:</strong> {{ ucfirst($compra->estado) }}</p>
                    <p><strong>Factura del proveedor:</strong> {{ $compra->factura_proveedor ?? '—' }}</p>
                    @if ($compra->recibida_en)
                        <p><strong>Recibida el:</strong> {{ $compra->recibida_en->format('d/m/Y H:i') }}</p>
                    @endif
                    @if ($compra->notas)
                        <p><strong>Notas:</strong> {{ $compra->notas }}</p>
                    @endif
                </div>

                @php $puedeRecibir = auth()->user()->can('recibir-compras') && $compra->puedeRecibirse(); @endphp

                <form method="POST" action="{{ route('compras.receive', $compra) }}" class="space-y-4">
                    @csrf @method('PATCH')

                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b text-left">
                                <th class="p-2">Ficha</th>
                                <th class="p-2">Costo unit.</th>
                                <th class="p-2">Pedida</th>
                                <th class="p-2">Recibida</th>
                                <th class="p-2">Pendiente</th>
                                @if ($puedeRecibir)
                                    <th class="p-2">Recibir ahora</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($compra->detalles as $detalle)
                                <tr class="border-b">
                                    <td class="p-2 font-medium">{{ $detalle->ficha?->denominacion }}</td>
                                    <td class="p-2">${{ number_format((float) $detalle->costo_unitario, 0, ',', '.') }}</td>
                                    <td class="p-2">{{ $detalle->cantidad }}</td>
                                    <td class="p-2">{{ $detalle->cantidad_recibida }}</td>
                                    <td class="p-2">{{ $detalle->pendiente() }}</td>
                                    @if ($puedeRecibir)
                                        <td class="p-2">
                                            <input type="number" name="items[{{ $detalle->id }}]" value="{{ old('items.'.$detalle->id, 0) }}" min="0" max="{{ $detalle->pendiente() }}" class="border-gray-300 rounded-md w-28">
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($puedeRecibir)
                        <div class="max-w-sm">
                            <x-input-label for="factura_proveedor" value="Factura del proveedor (opcional)" />
                            <x-text-input id="factura_proveedor" name="factura_proveedor" type="text" class="mt-1 block w-full" :value="old('factura_proveedor', $compra->factura_proveedor)" />
                        </div>
                        <x-primary-button>Registrar recepción</x-primary-button>
                    @endif
                </form>

                <div class="text-right space-y-1">
                    <p>Subtotal: <strong>${{ number_format((float) $compra->subtotal, 0, ',', '.') }}</strong></p>
                    <p>IVA (19%): <strong>${{ number_format((float) $compra->impuesto, 0, ',', '.') }}</strong></p>
                    <p class="text-lg">Total: <strong>${{ number_format((float) $compra->total, 0, ',', '.') }}</strong></p>
                </div>

                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('compras.pdf', $compra) }}" target="_blank" class="text-blue-700 underline">Descargar PDF</a>
                    <a href="{{ route('compras.index') }}" class="underline">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>