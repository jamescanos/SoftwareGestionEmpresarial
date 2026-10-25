<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel de control</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-amber-500">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold">¡Bienvenido, {{ auth()->user()->name }}!</h1>
                    <p class="text-gray-600">Casino Fortuna - Sistema de Gestión</p>
                    <p class="text-sm text-gray-500 mt-1">
                        Rol: {{ $roles->isNotEmpty() ? $roles->join(', ') : 'Sin rol asignado' }}
                    </p>
                </div>
            </div>

            @if ($stats->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($stats as $stat)
                        <div class="bg-white shadow-sm rounded-lg p-5 border-t-4 border-amber-500">
                            <p class="text-sm text-gray-500">{{ $stat['label'] }}</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $stat['value'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            @can('ver-ventas')
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h3 class="font-semibold mb-3">Top 5 fichas más vendidas</h3>
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="p-2">Ficha</th>
                                    <th class="p-2">Cantidad</th>
                                    <th class="p-2">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topFichas as $fila)
                                    <tr class="border-b">
                                        <td class="p-2">{{ $fila->denominacion }}</td>
                                        <td class="p-2">{{ $fila->cantidad }}</td>
                                        <td class="p-2">${{ number_format((float) $fila->valor, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="p-2 text-gray-500">Aún no hay ventas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h3 class="font-semibold mb-3">Top 5 clientes con más compras</h3>
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="p-2">Cliente</th>
                                    <th class="p-2">Compras</th>
                                    <th class="p-2">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topClientes as $fila)
                                    <tr class="border-b">
                                        <td class="p-2">{{ $fila->nombre }}</td>
                                        <td class="p-2">{{ $fila->compras }}</td>
                                        <td class="p-2">${{ number_format((float) $fila->total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="p-2 text-gray-500">Aún no hay ventas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endcan
            
            @if ($fichasReabastecer->isNotEmpty())
                <div class="bg-white shadow-sm rounded-lg p-6 border-l-4 border-red-600">
                    <h3 class="font-semibold mb-3">Fichas para reabastecer</h3>
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b text-left">
                                <th class="p-2">Ficha</th>
                                <th class="p-2">Stock</th>
                                @can('crear-compras')
                                    <th class="p-2">Acción</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fichasReabastecer as $ficha)
                                <tr class="border-b">
                                    <td class="p-2">{{ $ficha->denominacion }}</td>
                                    <td class="p-2">{{ $ficha->stock }}</td>
                                    @can('crear-compras')
                                        <td class="p-2">
                                            <a href="{{ route('compras.create', ['ficha' => $ficha->id]) }}" class="text-amber-700 underline">Crear compra</a>
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-3">Módulos disponibles</h3>
                <ul class="list-disc ps-6 space-y-1">
                    @forelse ($modules as $module)
                        <li><a href="{{ route($module['route']) }}" class="text-amber-700 underline">{{ $module['title'] }}</a></li>
                    @empty
                        <li class="text-gray-500">No tienes módulos disponibles. Pide a un administrador que te asigne un rol.</li>
                    @endforelse
                </ul>
            </div>

        </div>
    </div>
</x-app-layout>