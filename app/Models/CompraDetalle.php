<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraDetalle extends Model
{
    protected $fillable = [
        'compra_id', 'ficha_id', 'cantidad', 'cantidad_recibida', 'costo_unitario', 'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'cantidad_recibida' => 'integer',
            'costo_unitario' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    public function ficha()
    {
        return $this->belongsTo(Ficha::class)->withTrashed();
    }

    // Cantidad que todavía falta por recibir
    public function pendiente(): int
    {
        return $this->cantidad - $this->cantidad_recibida;
    }
}