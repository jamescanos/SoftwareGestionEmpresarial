<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="documento" value="Documento" />
        <x-text-input id="documento" name="documento" type="text" class="mt-1 block w-full" :value="old('documento', $cliente->documento)" required />
        <x-input-error class="mt-2" :messages="$errors->get('documento')" />
    </div>

    <div>
        <x-input-label for="nombre" value="Nombre completo" />
        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $cliente->nombre)" required />
        <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="fecha_nacimiento" value="Fecha de nacimiento" />
        <x-text-input id="fecha_nacimiento" name="fecha_nacimiento" type="date" class="mt-1 block w-full" :value="old('fecha_nacimiento', $cliente->fecha_nacimiento?->format('Y-m-d'))" required />
        <x-input-error class="mt-2" :messages="$errors->get('fecha_nacimiento')" />
    </div>

    <div>
        <x-input-label for="nivel_vip" value="Nivel VIP" />
        <select id="nivel_vip" name="nivel_vip" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            @foreach (['estandar' => 'Estándar', 'oro' => 'Oro', 'platino' => 'Platino'] as $value => $label)
                <option value="{{ $value }}" @selected(old('nivel_vip', $cliente->nivel_vip) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('nivel_vip')" />
    </div>
</div>

<div>
    <x-input-label for="saldo_fichas" value="Saldo de fichas" />
    <x-text-input id="saldo_fichas" name="saldo_fichas" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('saldo_fichas', $cliente->saldo_fichas)" required />
    <x-input-error class="mt-2" :messages="$errors->get('saldo_fichas')" />
</div>