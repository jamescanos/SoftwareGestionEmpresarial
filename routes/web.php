<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FichaController;
use App\Http\Controllers\JuegoController;
use App\Http\Controllers\MesaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Mesas
    Route::patch('mesas/{mesa}/restore', [MesaController::class, 'restore'])
        ->withTrashed()->name('mesas.restore');
    Route::resource('mesas', MesaController::class)->except('show');

    // Juegos
    Route::patch('juegos/{juego}/restore', [JuegoController::class, 'restore'])
        ->withTrashed()->name('juegos.restore');
    Route::resource('juegos', JuegoController::class)->except('show');

    // Clientes
    Route::patch('clientes/{cliente}/restore', [ClienteController::class, 'restore'])
        ->withTrashed()->name('clientes.restore');
    Route::resource('clientes', ClienteController::class)->except('show');

    // Fichas
    Route::get('fichas', [FichaController::class, 'index'])->name('fichas.index');

    // Ventas de fichas
    Route::get('ventas/{venta}/pdf', [VentaController::class, 'pdf'])->name('ventas.pdf');
    Route::resource('ventas', VentaController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    // Proveedores (Clase 10)
    // OJO: Laravel singulariza "proveedores" como "proveedore"; parameters() lo corrige a "proveedor"
    Route::patch('proveedores/{proveedor}/restore', [ProveedorController::class, 'restore'])
        ->withTrashed()->name('proveedores.restore');
    Route::resource('proveedores', ProveedorController::class)
        ->parameters(['proveedores' => 'proveedor'])
        ->except('show');

    // Compras (Clase 10)
    Route::patch('compras/{compra}/receive', [CompraController::class, 'receive'])->name('compras.receive');
    Route::get('compras/{compra}/pdf', [CompraController::class, 'pdf'])->name('compras.pdf');
    Route::resource('compras', CompraController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
});

require __DIR__.'/auth.php';