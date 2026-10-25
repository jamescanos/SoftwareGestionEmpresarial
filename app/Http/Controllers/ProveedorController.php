<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProveedorController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-proveedores', only: ['index']),
            new Middleware('permission:crear-proveedores', only: ['create', 'store']),
            new Middleware('permission:editar-proveedores', only: ['edit', 'update']),
            new Middleware('permission:eliminar-proveedores', only: ['destroy', 'restore']),
        ];
    }

    public function index(Request $request)
    {
        $showTrashed = $request->boolean('trashed') && $request->user()->can('eliminar-proveedores');

        $proveedores = Proveedor::query()
            ->withCount('compras')
            ->when($showTrashed, fn ($q) => $q->onlyTrashed())
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%'.$request->string('search').'%';
                $q->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', $search)
                      ->orWhere('numero_documento', 'like', $search)
                      ->orWhere('contacto', 'like', $search);
                });
            })
            ->when($request->filled('estado'), fn ($q) => $q->where('activo', $request->input('estado') === 'activo'))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('proveedores.index', [
            'proveedores' => $proveedores,
            'showTrashed' => $showTrashed,
            'trashedCount' => Proveedor::onlyTrashed()->count(),
        ]);
    }

    public function create()
    {
        return view('proveedores.create', [
            'proveedor' => new Proveedor(['activo' => true, 'tipo_documento' => 'NIT']),
        ]);
    }

    public function store(StoreProveedorRequest $request)
    {
        Proveedor::create($request->validated());

        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado exitosamente.');
    }

    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', ['proveedor' => $proveedor]);
    }

    public function update(UpdateProveedorRequest $request, Proveedor $proveedor)
    {
        $proveedor->update($request->validated());

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        // Regla: no se elimina un proveedor con compras asociadas
        if ($proveedor->compras()->exists()) {
            return back()->with('error', "No se puede eliminar «{$proveedor->nombre}»: tiene compras asociadas.");
        }

        $proveedor->delete();

        return redirect()->route('proveedores.index')
            ->with('success', "Proveedor «{$proveedor->nombre}» enviado a la papelera.");
    }

    public function restore(Proveedor $proveedor)
    {
        $proveedor->restore();

        return redirect()->route('proveedores.index', ['trashed' => 1])
            ->with('success', "Proveedor «{$proveedor->nombre}» restaurado.");
    }
}