<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ClienteController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-clientes', only: ['index']),
            new Middleware('permission:crear-clientes', only: ['create', 'store']),
            new Middleware('permission:editar-clientes', only: ['edit', 'update']),
            new Middleware('permission:eliminar-clientes', only: ['destroy', 'restore']),
        ];
    }

    public function index(Request $request)
    {
        $showTrashed = $request->boolean('trashed') && $request->user()->can('eliminar-clientes');

        $clientes = Cliente::query()
        ->withCount('ventas')
            ->when($showTrashed, fn ($q) => $q->onlyTrashed())
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%'.$request->string('search').'%';
                $q->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', $search)
                      ->orWhere('documento', 'like', $search);
                });
            })
            ->when($request->filled('nivel'), fn ($q) => $q->where('nivel_vip', $request->input('nivel')))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('clientes.index', [
            'clientes' => $clientes,
            'showTrashed' => $showTrashed,
            'trashedCount' => Cliente::onlyTrashed()->count(),
        ]);
    }

    public function create()
    {
        return view('clientes.create', [
            'cliente' => new Cliente(['nivel_vip' => 'estandar', 'saldo_fichas' => 0]),
        ]);
    }

    public function store(StoreClienteRequest $request)
    {
        Cliente::create($request->validated());

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', ['cliente' => $cliente]);
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        $cliente->update($request->validated());

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

        public function destroy(Cliente $cliente)
    {
        // Regla de negocio: no se puede eliminar un cliente con ventas asociadas
        if ($cliente->ventas()->exists()) {
            return back()->with('error', "No se puede eliminar «{$cliente->nombre}»: tiene ventas asociadas.");
        }

        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', "Cliente «{$cliente->nombre}» enviado a la papelera.");
    }

    public function restore(Cliente $cliente)
    {
        $cliente->restore();

        return redirect()->route('clientes.index', ['trashed' => 1])
            ->with('success', "Cliente «{$cliente->nombre}» restaurado.");
    }
}