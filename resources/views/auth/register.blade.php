<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="text-xl font-semibold text-gray-800">Crear cuenta</h2>
        <p class="text-sm text-gray-500">Únete al sistema de gestión de Casino Fortuna</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                class="block mt-1 w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
        </div>

        <div class="mt-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                class="block mt-1 w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
        </div>

        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="block mt-1 w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
        </div>

        <div class="mt-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                class="block mt-1 w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                ¿Ya tienes una cuenta?
            </a>

            <button type="submit" class="ms-4 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700">
                Registrarme
            </button>
        </div>
    </form>
</x-guest-layout>