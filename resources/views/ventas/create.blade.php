<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva venta de fichas</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('ventas.store') }}" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                @csrf

                @if ($errors->any())
                    <div class="p-3 rounded bg-red-100 text-red-800">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="cliente_id" value="Cliente" />
                        <select id="cliente_id" name="cliente_id" class="mt-1 block w-full border-gray-300 rounded-md" required>
                            <option value="">Seleccione un cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}" @selected(old('cliente_id') == $cliente->id)>{{ $cliente->nombre }} — {{ $cliente->documento }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="fecha_venta" value="Fecha de venta" />
                        <x-text-input id="fecha_venta" name="fecha_venta" type="date" class="mt-1 block w-full" :value="old('fecha_venta', now()->format('Y-m-d'))" required />
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold">Fichas</h3>
                        <button type="button" id="add-row" class="text-amber-700 underline">+ Agregar ficha</button>
                    </div>
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b text-left">
                                <th class="p-2">Ficha</th>
                                <th class="p-2">Cantidad</th>
                                <th class="p-2">Valor unit.</th>
                                <th class="p-2">Subtotal</th>
                                <th class="p-2"></th>
                            </tr>
                        </thead>
                        <tbody id="rows"></tbody>
                    </table>
                </div>

                <div>
                    <x-input-label for="notas" value="Notas (opcional)" />
                    <textarea id="notas" name="notas" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('notas') }}</textarea>
                </div>

                <div class="text-right space-y-1">
                    <p>Subtotal: <strong id="subtotal">$0</strong></p>
                    <p>IVA (19%): <strong id="iva">$0</strong></p>
                    <p class="text-lg">Total: <strong id="total">$0</strong></p>
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>Registrar venta</x-primary-button>
                    <a href="{{ route('ventas.index') }}" class="underline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const fichas = @json($fichas);
        const oldItems = @json(old('items', []));
        const IVA = 0.19;
        const rows = document.getElementById('rows');
        let indice = 0;

        const dinero = (n) => '$' + Math.round(n).toLocaleString('es-CO');

        function opciones(seleccionada) {
            return '<option value="">Seleccione</option>' + fichas.map(f =>
                `<option value="${f.id}" ${String(seleccionada) === String(f.id) ? 'selected' : ''}>${f.denominacion} (stock: ${f.stock})</option>`
            ).join('');
        }

        function agregarFila(fichaId = '', cantidad = 1) {
            const i = indice++;
            const tr = document.createElement('tr');
            tr.className = 'border-b';
            tr.innerHTML = `
                <td class="p-2"><select name="items[${i}][ficha_id]" class="ficha border-gray-300 rounded-md w-full" required>${opciones(fichaId)}</select></td>
                <td class="p-2"><input type="number" name="items[${i}][cantidad]" value="${cantidad}" min="1" class="cantidad border-gray-300 rounded-md w-24" required></td>
                <td class="p-2 valor">$0</td>
                <td class="p-2 sub">$0</td>
                <td class="p-2"><button type="button" class="quitar text-red-700 underline">Quitar</button></td>`;
            rows.appendChild(tr);
            recalcular();
        }

        function recalcular() {
            let subtotal = 0;
            rows.querySelectorAll('tr').forEach(tr => {
                const ficha = fichas.find(f => String(f.id) === tr.querySelector('.ficha').value);
                const cantidad = parseInt(tr.querySelector('.cantidad').value) || 0;
                const valor = ficha ? parseFloat(ficha.valor) : 0;
                const sub = valor * cantidad;
                tr.querySelector('.valor').textContent = dinero(valor);
                tr.querySelector('.sub').textContent = dinero(sub);
                subtotal += sub;
            });
            document.getElementById('subtotal').textContent = dinero(subtotal);
            document.getElementById('iva').textContent = dinero(subtotal * IVA);
            document.getElementById('total').textContent = dinero(subtotal * (1 + IVA));
        }

        document.getElementById('add-row').addEventListener('click', () => agregarFila());
        rows.addEventListener('input', recalcular);
        rows.addEventListener('change', recalcular);
        rows.addEventListener('click', (e) => {
            if (e.target.classList.contains('quitar')) {
                e.target.closest('tr').remove();
                recalcular();
            }
        });

        // Si el servidor rechazó la venta, se reconstruyen las filas con lo que el usuario había escrito
        const anteriores = Object.values(oldItems);
        if (anteriores.length) {
            anteriores.forEach(it => agregarFila(it.ficha_id, it.cantidad));
        } else {
            agregarFila();
        }
    </script>
</x-app-layout>