<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo juego</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('juegos.store') }}" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                @csrf
                @include('juegos.partials.form')
                <div class="flex items-center gap-4">
                    <x-primary-button>Guardar</x-primary-button>
                    <a href="{{ route('juegos.index') }}" class="underline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>