<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Ficha;
use App\Models\Mesa;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $modules = collect([
            ['title' => 'Mesas', 'route' => 'mesas.index', 'permission' => 'ver-mesas'],
            ['title' => 'Juegos', 'route' => 'juegos.index', 'permission' => 'ver-juegos'],
            ['title' => 'Clientes', 'route' => 'clientes.index', 'permission' => 'ver-clientes'],
            ['title' => 'Fichas', 'route' => 'fichas.index', 'permission' => 'ver-fichas'],
            ['title' => 'Ventas', 'route' => 'ventas.index', 'permission' => 'ver-ventas'],
            ['title' => 'Proveedores', 'route' => 'proveedores.index', 'permission' => 'ver-proveedores'],
            ['title' => 'Compras', 'route' => 'compras.index', 'permission' => 'ver-compras'],
        ])->filter(fn ($m) => $user->can($m['permission']))->values();

        $stats = collect();

        if ($user->can('ver-mesas')) {
            $stats->push(['label' => 'Mesas abiertas', 'value' => Mesa::where('estado', 'abierta')->count()]);
        }

        if ($user->can('ver-clientes')) {
            $stats->push(['label' => 'Clientes registrados', 'value' => Cliente::count()]);
            $stats->push(['label' => 'Saldo de fichas de clientes', 'value' => '$ '.number_format((float) Cliente::sum('saldo_fichas'), 0, ',', '.')]);
        }

        if ($user->can('ver-fichas')) {
            $stats->push(['label' => 'Fichas con stock bajo (≤ 5)', 'value' => Ficha::where('activo', true)->where('stock', '<=', 5)->count()]);
            $stats->push(['label' => 'Valor de fichas en caja', 'value' => '$ '.number_format((float) Ficha::where('activo', true)->sum(DB::raw('valor * stock')), 0, ',', '.')]);
        }

        $topFichas = collect();
        $topClientes = collect();

        if ($user->can('ver-ventas')) {
            $stats->push([
                'label' => 'Ventas del mes',
                'value' => Venta::whereMonth('fecha_venta', now()->month)->whereYear('fecha_venta', now()->year)->count(),
            ]);
            $stats->push([
                'label' => 'Total vendido del mes',
                'value' => '$ '.number_format((float) Venta::whereMonth('fecha_venta', now()->month)->whereYear('fecha_venta', now()->year)->sum('total'), 0, ',', '.'),
            ]);

            $topFichas = VentaDetalle::query()
                ->join('ventas', 'ventas.id', '=', 'venta_detalles.venta_id')
                ->join('fichas', 'fichas.id', '=', 'venta_detalles.ficha_id')
                ->whereNull('ventas.deleted_at')
                ->select('fichas.denominacion', DB::raw('SUM(venta_detalles.cantidad) as cantidad'), DB::raw('SUM(venta_detalles.subtotal) as valor'))
                ->groupBy('fichas.id', 'fichas.denominacion')
                ->orderByDesc('cantidad')
                ->limit(5)
                ->get();

            $topClientes = Venta::query()
                ->join('clientes', 'clientes.id', '=', 'ventas.cliente_id')
                ->select('clientes.nombre', DB::raw('COUNT(ventas.id) as compras'), DB::raw('SUM(ventas.total) as total'))
                ->groupBy('clientes.id', 'clientes.nombre')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
        }

        // Clase 10: KPIs de compras (abastecimiento)
        if ($user->can('ver-compras')) {
            $porRecibir = Compra::whereIn('estado', ['pendiente', 'parcial']);

            $stats->push(['label' => 'Órdenes por recibir', 'value' => (clone $porRecibir)->count()]);
            $stats->push(['label' => 'Valor por recibir', 'value' => '$ '.number_format((float) (clone $porRecibir)->sum('total'), 0, ',', '.')]);
            $stats->push([
                'label' => 'Comprado en el mes',
                'value' => '$ '.number_format((float) Compra::whereNot('estado', 'anulada')->whereMonth('fecha_compra', now()->month)->whereYear('fecha_compra', now()->year)->sum('total'), 0, ',', '.'),
            ]);
        }

        // Tabla accionable: fichas para reabastecer
        $fichasReabastecer = $user->can('ver-fichas')
            ? Ficha::where('activo', true)->where('stock', '<=', 5)->orderBy('stock')->get()
            : collect();

        return view('dashboard', [
            'roles' => $user->getRoleNames(),
            'modules' => $modules,
            'stats' => $stats,
            'topFichas' => $topFichas,
            'topClientes' => $topClientes,
            'fichasReabastecer' => $fichasReabastecer,
        ]);
    }
}