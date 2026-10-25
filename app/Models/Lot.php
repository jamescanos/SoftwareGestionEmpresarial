<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    protected $fillable = ['product_id', 'lot_number', 'expiration_date', 'quantity'];

    protected function casts(): array
    {
        return [
            'expiration_date' => 'date',
            'quantity' => 'integer',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}