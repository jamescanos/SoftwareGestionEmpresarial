<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compra extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'numero_compra', 'proveedor_id', 'user_id', 'fecha_compra', 'factura_proveedor',
        'subtotal', 'impuesto', 'total', 'estado', 'recibida_en', 'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_compra' => 'date',
            'recibida_en' => 'datetime',
            'subtotal' => 'decimal:2',
            'impuesto' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(CompraDetalle::class);
    }

    // ¿Todavía se pueden recibir fichas de esta orden?
    public function puedeRecibirse(): bool
    {
        return in_array($this->estado, ['pendiente', 'parcial']);
    }

    // Regla: solo se anula una orden a la que no ha llegado nada
    public function puedeAnularse(): bool
    {
        return $this->estado === 'pendiente';
    }
}