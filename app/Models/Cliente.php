<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'documento', 'nombre', 'fecha_nacimiento', 'nivel_vip', 'saldo_fichas'
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'saldo_fichas' => 'decimal:2',
        ];
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}