<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecibirCompraRequest;
use App\Http\Requests\StoreCompraRequest;
use App\Models\Compra;
use App\Models\Ficha;
use App\Models\Proveedor;
use App\Services\CompraService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\ValidationException;

class CompraController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-compras', only: ['index', 'show', 'pdf']),
            new Middleware('permission:crear-compras', only: ['create', 'store']),
            new Middleware('permission:recibir-compras', only: ['receive']),
            new Middleware('permission:eliminar-compras', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $compras = Compra::query()
            ->with(['proveedor', 'user'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%'.$request->string('search').'%';
                $q->where(function ($q) use ($search) {
                    $q->where('numero_compra', 'like', $search)
                      ->orWhere('factura_proveedor', 'like', $search)
                      ->orWhereHas('proveedor', fn ($q) => $q->where('nombre', 'like', $search));
                });
            })
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->input('estado')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('fecha_compra', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('fecha_compra', '<=', $request->input('to')))
            ->orderByDesc('fecha_compra')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('compras.index', [
            'compras' => $compras,
            'totalMes' => Compra::whereMonth('fecha_compra', now()->month)
                ->whereYear('fecha_compra', now()->year)
                ->sum('total'),
            'pendientes' => Compra::whereIn('estado', ['pendiente', 'parcial'])->count(),
        ]);
    }

    public function create(Request $request)
    {
        return view('compras.create', [
            'proveedores' => Proveedor::where('activo', true)->orderBy('nombre')->get(['id', 'nombre', 'numero_documento']),
            // Aquí SÍ se listan fichas con stock 0 o bajo: son las que más se necesita comprar
            'fichas' => Ficha::where('activo', true)->orderBy('valor')->get(['id', 'denominacion', 'valor', 'stock']),
            'fichaSeleccionada' => $request->input('ficha'),
        ]);
    }

    public function store(StoreCompraRequest $request, CompraService $service)
    {
        $compra = $service->registrar($request->validated(), $request->user()->id);

        return redirect()->route('compras.show', $compra)
            ->with('success', "Orden de compra «{$compra->numero_compra}» registrada. Pendiente de recepción.");
    }

    public function show(Compra $compra)
    {
        $compra->load(['proveedor', 'user', 'detalles.ficha']);

        return view('compras.show', ['compra' => $compra]);
    }

    public function receive(RecibirCompraRequest $request, Compra $compra, CompraService $service)
    {
        try {
            $compra = $service->recibir(
                $compra,
                $request->validated('items'),
                $request->validated('factura_proveedor')
            );
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $mensaje = $compra->estado === 'recibida'
            ? "Orden «{$compra->numero_compra}» recibida completamente. Stock de fichas actualizado."
            : "Recepción parcial registrada para «{$compra->numero_compra}». Stock de fichas actualizado.";

        return redirect()->route('compras.show', $compra)->with('success', $mensaje);
    }

    public function destroy(Compra $compra, CompraService $service)
    {
        try {
            $service->anular($compra);
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        return redirect()->route('compras.index')
            ->with('success', "Orden «{$compra->numero_compra}» anulada.");
    }

    public function pdf(Compra $compra)
    {
        $compra->load(['proveedor', 'user', 'detalles.ficha']);

        return Pdf::loadView('pdf.compra', ['compra' => $compra])
            ->setPaper('letter')
            ->stream("{$compra->numero_compra}.pdf");
    }
}