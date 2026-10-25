<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentaRequest;
use App\Models\Cliente;
use App\Models\Ficha;
use App\Models\Venta;
use App\Services\VentaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-ventas', only: ['index', 'show', 'pdf']),
            new Middleware('permission:crear-ventas', only: ['create', 'store']),
            new Middleware('permission:eliminar-ventas', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $ventas = Venta::query()
            ->with(['cliente', 'user'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%'.$request->string('search').'%';
                $q->where(function ($q) use ($search) {
                    $q->where('numero_factura', 'like', $search)
                      ->orWhereHas('cliente', fn ($q) => $q->where('nombre', 'like', $search));
                });
            })
            ->when($request->filled('from'), fn ($q) => $q->whereDate('fecha_venta', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('fecha_venta', '<=', $request->input('to')))
            ->orderByDesc('fecha_venta')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('ventas.index', [
            'ventas' => $ventas,
            'totalMes' => Venta::whereMonth('fecha_venta', now()->month)
                ->whereYear('fecha_venta', now()->year)
                ->sum('total'),
        ]);
    }

    public function create()
    {
        return view('ventas.create', [
            'clientes' => Cliente::orderBy('nombre')->get(['id', 'nombre', 'documento']),
            'fichas' => Ficha::where('activo', true)->where('stock', '>', 0)->orderBy('valor')
                ->get(['id', 'denominacion', 'valor', 'stock']),
        ]);
    }

    public function store(StoreVentaRequest $request, VentaService $service)
    {
        try {
            $venta = $service->registrar($request->validated(), $request->user()->id);

            return redirect()->route('ventas.show', $venta)
                ->with('success', "Venta «{$venta->numero_factura}» registrada correctamente.");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'user', 'detalles.ficha']);

        return view('ventas.show', ['venta' => $venta]);
    }

    public function destroy(Venta $venta)
    {
        DB::transaction(function () use ($venta) {
            foreach ($venta->detalles as $detalle) {
                $detalle->ficha?->increment('stock', $detalle->cantidad);
            }

            $venta->update(['estado' => 'anulada']);
            $venta->delete();
        });

        return redirect()->route('ventas.index')
            ->with('success', "Venta «{$venta->numero_factura}» anulada y fichas devueltas al stock.");
    }

    // Clase 10: factura de venta en PDF
    public function pdf(Venta $venta)
    {
        $venta->load(['cliente', 'user', 'detalles.ficha']);

        return Pdf::loadView('pdf.venta', ['venta' => $venta])
            ->setPaper('letter')
            ->stream("{$venta->numero_factura}.pdf");
    }
}