<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar proveedor</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('proveedores.update', $proveedor) }}" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                @csrf @method('PUT')
                @include('proveedores.partials.form')
                <div class="flex items-center gap-4">
                    <x-primary-button>Actualizar</x-primary-button>
                    <a href="{{ route('proveedores.index') }}" class="underline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
