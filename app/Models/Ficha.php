<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ficha extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'denominacion', 'valor', 'stock', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'stock' => 'integer',
            'activo' => 'boolean',
        ];
    }

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function compraDetalles()
    {
        return $this->hasMany(CompraDetalle::class);
    }
}