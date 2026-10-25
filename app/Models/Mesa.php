<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mesa extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'juego_id', 'estado'
    ];

    // Relación: una Mesa pertenece a un Juego (incluso si el juego está en la papelera)
    public function juego()
    {
        return $this->belongsTo(Juego::class)->withTrashed();
    }
}