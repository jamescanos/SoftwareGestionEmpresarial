<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJuegoRequest;
use App\Http\Requests\UpdateJuegoRequest;
use App\Models\Juego;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class JuegoController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-juegos', only: ['index']),
            new Middleware('permission:crear-juegos', only: ['create', 'store']),
            new Middleware('permission:editar-juegos', only: ['edit', 'update']),
            new Middleware('permission:eliminar-juegos', only: ['destroy', 'restore']),
        ];
    }

    public function index(Request $request)
    {
        $showTrashed = $request->boolean('trashed') && $request->user()->can('eliminar-juegos');

        $juegos = Juego::query()
            ->withCount('mesas')
            ->when($showTrashed, fn ($query) => $query->onlyTrashed())
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('juegos.index', [
            'juegos' => $juegos,
            'showTrashed' => $showTrashed,
            'trashedCount' => Juego::onlyTrashed()->count(),
        ]);
    }

    public function create()
    {
        return view('juegos.create', [
            'juego' => new Juego(['activo' => true]),
        ]);
    }

    public function store(StoreJuegoRequest $request)
    {
        Juego::create($request->validated());

        return redirect()->route('juegos.index')
            ->with('success', 'Juego creado exitosamente.');
    }

    public function edit(Juego $juego)
    {
        return view('juegos.edit', [
            'juego' => $juego,
        ]);
    }

    public function update(UpdateJuegoRequest $request, Juego $juego)
    {
        $juego->update($request->validated());

        return redirect()->route('juegos.index')
            ->with('success', 'Juego actualizado exitosamente.');
    }

    public function destroy(Juego $juego)
    {
        // No se permite eliminar un juego que todavía tiene mesas activas
        if ($juego->mesas()->exists()) {
            return back()->with('error', "No se puede eliminar «{$juego->nombre}»: tiene mesas asociadas.");
        }

        $juego->delete();

        return redirect()->route('juegos.index')
            ->with('success', "Juego «{$juego->nombre}» enviado a la papelera.");
    }

    public function restore(Juego $juego)
    {
        $juego->restore();

        return redirect()->route('juegos.index', ['trashed' => 1])
            ->with('success', "Juego «{$juego->nombre}» restaurado.");
    }
}