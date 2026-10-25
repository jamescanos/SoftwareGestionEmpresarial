<div>
    <x-input-label for="juego_id" value="Juego" />
    <select id="juego_id" name="juego_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        <option value="">Selecciona un juego</option>
        @foreach ($juegos as $juego)
            <option value="{{ $juego->id }}" @selected(old('juego_id', $mesa->juego_id) == $juego->id)>{{ $juego->nombre }}</option>
        @endforeach
    </select>
    <x-input-error class="mt-2" :messages="$errors->get('juego_id')" />
</div>

<div>
    <x-input-label for="estado" value="Estado" />
    <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        <option value="abierta" @selected(old('estado', $mesa->estado) == 'abierta')>Abierta</option>
        <option value="cerrada" @selected(old('estado', $mesa->estado) == 'cerrada')>Cerrada</option>
    </select>
    <x-input-error class="mt-2" :messages="$errors->get('estado')" />
</div>