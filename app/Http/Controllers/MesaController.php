<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMesaRequest;
use App\Http\Requests\UpdateMesaRequest;
use App\Models\Juego;
use App\Models\Mesa;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MesaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-mesas', only: ['index']),
            new Middleware('permission:crear-mesas', only: ['create', 'store']),
            new Middleware('permission:editar-mesas', only: ['edit', 'update']),
            new Middleware('permission:eliminar-mesas', only: ['destroy', 'restore']),
        ];
    }

    public function index(Request $request)
    {
        $showTrashed = $request->boolean('trashed') && $request->user()->can('eliminar-mesas');

        $mesas = Mesa::query()
            ->with('juego')
            ->when($showTrashed, fn ($query) => $query->onlyTrashed())
            ->latest($showTrashed ? 'deleted_at' : 'created_at')
            ->paginate(10)
            ->withQueryString();

        return view('mesas.index', [
            'mesas' => $mesas,
            'showTrashed' => $showTrashed,
            'trashedCount' => Mesa::onlyTrashed()->count(),
        ]);
    }

    public function create()
    {
        return view('mesas.create', [
            'mesa' => new Mesa(['estado' => 'abierta']),
            'juegos' => $this->selectableJuegos(),
        ]);
    }

    public function store(StoreMesaRequest $request)
    {
        Mesa::create($request->validated());

        return redirect()->route('mesas.index')
            ->with('success', 'Mesa creada exitosamente.');
    }

    public function edit(Mesa $mesa)
    {
        return view('mesas.edit', [
            'mesa' => $mesa,
            'juegos' => $this->selectableJuegos(),
        ]);
    }

    public function update(UpdateMesaRequest $request, Mesa $mesa)
    {
        $mesa->update($request->validated());

        return redirect()->route('mesas.index')
            ->with('success', 'Mesa actualizada exitosamente.');
    }

    public function destroy(Mesa $mesa)
    {
        $mesa->delete();

        return redirect()->route('mesas.index')
            ->with('success', "Mesa #{$mesa->id} enviada a la papelera.");
    }

    public function restore(Mesa $mesa)
    {
        if ($mesa->juego?->trashed()) {
            return back()->with('error', "Restaura primero el juego «{$mesa->juego->nombre}».");
        }

        $mesa->restore();

        return redirect()->route('mesas.index', ['trashed' => 1])
            ->with('success', "Mesa #{$mesa->id} restaurada.");
    }

    // Solo los juegos activos se pueden asignar a una mesa
    private function selectableJuegos()
    {
        return Juego::where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);
    }
}