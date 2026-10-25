<?php

namespace App\Services;

use App\Models\Ficha;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaService
{
    // IVA del 19% (como pide la guía del curso)
    public const TASA_IMPUESTO = 0.19;

    /**
     * Registra una venta completa dentro de una transacción.
     *
     * @param array $data ['cliente_id' => int, 'fecha_venta' => 'Y-m-d', 'notas' => string,
     *                     'items' => [['ficha_id' => int, 'cantidad' => int], ...]]
     */
    public function registrar(array $data, int $userId): Venta
    {
        return DB::transaction(function () use ($data, $userId) {
            // 0. Agrupar cantidades por ficha (si la misma ficha se agregó en dos filas)
            $cantidades = [];
            foreach ($data['items'] as $item) {
                $cantidades[$item['ficha_id']] = ($cantidades[$item['ficha_id']] ?? 0) + (int) $item['cantidad'];
            }

            // 1. Validar stock y calcular subtotales
            $lineas = [];
            $subtotal = 0;

            foreach ($cantidades as $fichaId => $cantidad) {
                // lockForUpdate(): bloquea la fila hasta terminar la transacción
                $ficha = Ficha::lockForUpdate()->findOrFail($fichaId);

                if (! $ficha->activo) {
                    throw ValidationException::withMessages([
                        'items' => "La ficha «{$ficha->denominacion}» está inactiva y no se puede vender.",
                    ]);
                }

                if ($ficha->stock < $cantidad) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para «{$ficha->denominacion}». Disponible: {$ficha->stock}, solicitado: {$cantidad}.",
                    ]);
                }

                $subtotalLinea = $ficha->valor * $cantidad;
                $subtotal += $subtotalLinea;

                $lineas[] = [
                    'ficha' => $ficha,
                    'cantidad' => $cantidad,
                    'valor_unitario' => $ficha->valor,
                    'subtotal' => $subtotalLinea,
                ];
            }

            $impuesto = round($subtotal * self::TASA_IMPUESTO, 2);
            $total = round($subtotal + $impuesto, 2);

            // 2. Crear la cabecera de la venta
            $venta = Venta::create([
                'numero_factura' => $this->siguienteNumeroFactura(),
                'cliente_id' => $data['cliente_id'],
                'user_id' => $userId,
                'fecha_venta' => $data['fecha_venta'],
                'subtotal' => $subtotal,
                'impuesto' => $impuesto,
                'total' => $total,
                'estado' => 'pagada',
                'notas' => $data['notas'] ?? null,
            ]);

            // 3. Crear los detalles y descontar el stock
            foreach ($lineas as $linea) {
                $venta->detalles()->create([
                    'ficha_id' => $linea['ficha']->id,
                    'cantidad' => $linea['cantidad'],
                    'valor_unitario' => $linea['valor_unitario'],
                    'subtotal' => $linea['subtotal'],
                ]);

                $linea['ficha']->decrement('stock', $linea['cantidad']);
            }

            return $venta;
        });
    }

    // Genera el siguiente número de factura: FV-00001, FV-00002, ...
    private function siguienteNumeroFactura(): string
    {
        $ultima = Venta::withTrashed()->orderByDesc('id')->first();
        $siguiente = $ultima ? $ultima->id + 1 : 1;

        return 'FV-'.str_pad($siguiente, 5, '0', STR_PAD_LEFT);
    }
}
