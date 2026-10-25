<div>
    <x-input-label for="nombre" value="Nombre" />
    <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $juego->nombre)" required />
    <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
</div>

<div>
    <x-input-label for="apuesta_min" value="Apuesta mínima" />
    <x-text-input id="apuesta_min" name="apuesta_min" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('apuesta_min', $juego->apuesta_min)" required />
    <x-input-error class="mt-2" :messages="$errors->get('apuesta_min')" />
</div>

<div>
    <x-input-label for="apuesta_max" value="Apuesta máxima" />
    <x-text-input id="apuesta_max" name="apuesta_max" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('apuesta_max', $juego->apuesta_max)" required />
    <x-input-error class="mt-2" :messages="$errors->get('apuesta_max')" />
</div>

<div>
    <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="activo" value="1" class="rounded border-gray-300" @checked(old('activo', $juego->activo))>
        <span>Juego activo</span>
    </label>
</div>