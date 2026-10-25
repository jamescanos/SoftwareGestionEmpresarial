<?php

namespace App\Http\Controllers;

use App\Models\Ficha;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FichaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-fichas', only: ['index']),
        ];
    }

    public function index()
    {
        return view('fichas.index', [
            'fichas' => Ficha::orderBy('valor')->get(),
        ]);
    }
}