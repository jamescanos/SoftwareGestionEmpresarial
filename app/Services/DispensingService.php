<?php

namespace App\Services;

use App\Models\Lot;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DispensingService
{
    /**
     * Despacha $quantity unidades de un producto aplicando FEFO
     * (First Expired, First Out). Devuelve de qué lotes salió cada unidad.
     */
    public function dispense(Product $product, int $quantity): array
    {
        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'La cantidad debe ser mayor que cero.',
            ]);
        }

        return DB::transaction(function () use ($product, $quantity) {
            // 1. Solo lotes NO vencidos y con unidades; el que vence antes va primero (FEFO).
            //    lockForUpdate: nadie más descuenta de estos lotes mientras dura la transacción.
            $lots = Lot::where('product_id', $product->id)
                ->where('expiration_date', '>=', today()->toDateString())
                ->where('quantity', '>', 0)
                ->orderBy('expiration_date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            // 2. Validar ANTES de descontar: si no alcanza, no se toca nada.
            $available = (int) $lots->sum('quantity');

            if ($available < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => "Stock insuficiente de «{$product->name}». "
                        ."Disponible sin vencidos: {$available}, solicitado: {$quantity}.",
                ]);
            }

            // 3. Descontar lote por lote hasta completar la cantidad.
            $remaining = $quantity;
            $used = [];

            foreach ($lots as $lot) {
                if ($remaining === 0) {
                    break;
                }

                $take = min($lot->quantity, $remaining);
                $lot->decrement('quantity', $take);
                $remaining -= $take;

                $used[] = [
                    'lot_id' => $lot->id,
                    'lot_number' => $lot->lot_number,
                    'quantity' => $take,
                ];
            }

            // 4. Descontar también el stock total del producto.
            $product->decrement('stock', $quantity);

            return $used;
        });
    }
}