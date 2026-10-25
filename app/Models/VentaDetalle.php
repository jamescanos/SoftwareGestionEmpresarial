<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    protected $fillable = [
        'venta_id', 'ficha_id', 'cantidad', 'valor_unitario', 'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'valor_unitario' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function ficha()
    {
        return $this->belongsTo(Ficha::class)->withTrashed();
    }
}