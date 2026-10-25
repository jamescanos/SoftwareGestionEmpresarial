<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\Ficha;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CompraService
{
    public const TASA_IMPUESTO = 0.19;

    /**
     * Registra una ORDEN DE COMPRA (estado "pendiente"). NO mueve el stock:
     * las fichas solo se suman cuando llegan (método recibir).
     */
    public function registrar(array $data, int $userId): Compra
    {
        return DB::transaction(function () use ($data, $userId) {
            $lineas = [];
            $subtotal = 0;

            foreach ($data['items'] as $item) {
                $cantidad = (int) $item['cantidad'];
                $costo = round((float) $item['costo_unitario'], 2);
                $subtotalLinea = round($cantidad * $costo, 2);

                $lineas[] = [
                    'ficha_id' => $item['ficha_id'],
                    'cantidad' => $cantidad,
                    'costo_unitario' => $costo,
                    'subtotal' => $subtotalLinea,
                ];

                $subtotal += $subtotalLinea;
            }

            $impuesto = round($subtotal * self::TASA_IMPUESTO, 2);
            $total = round($subtotal + $impuesto, 2);

            $compra = Compra::create([
                'numero_compra' => $this->siguienteNumero(),
                'proveedor_id' => $data['proveedor_id'],
                'user_id' => $userId,
                'fecha_compra' => $data['fecha_compra'],
                'factura_proveedor' => $data['factura_proveedor'] ?? null,
                'subtotal' => $subtotal,
                'impuesto' => $impuesto,
                'total' => $total,
                'estado' => 'pendiente',
                'notas' => $data['notas'] ?? null,
            ]);

            $compra->detalles()->createMany($lineas);

            return $compra;
        });
    }

    /**
     * Registra la RECEPCIÓN (total o parcial) y AUMENTA el stock de fichas.
     *
     * @param array $cantidades [id_del_detalle => cantidad_que_llega, ...]
     */
    public function recibir(Compra $compra, array $cantidades, ?string $facturaProveedor = null): Compra
    {
        return DB::transaction(function () use ($compra, $cantidades, $facturaProveedor) {
            // Bloqueo: dos cajeros no pueden recibir la misma orden a la vez
            $compra = Compra::lockForUpdate()->findOrFail($compra->id);
            $compra->load('detalles.ficha');

            // Regla 1: solo se reciben órdenes pendientes o parciales
            if (! $compra->puedeRecibirse()) {
                throw ValidationException::withMessages([
                    'items' => "La orden {$compra->numero_compra} está «{$compra->estado}» y no admite recepciones.",
                ]);
            }

            // Regla 2: las líneas enviadas deben pertenecer a esta orden
            $desconocidos = array_diff(array_keys($cantidades), $compra->detalles->pluck('id')->all());
            if (! empty($desconocidos)) {
                throw ValidationException::withMessages([
                    'items' => 'Se enviaron líneas que no pertenecen a esta orden de compra.',
                ]);
            }

            $recibioAlgo = false;

            foreach ($compra->detalles as $detalle) {
                $cantidad = (int) ($cantidades[$detalle->id] ?? 0);

                if ($cantidad === 0) {
                    continue;
                }

                // Regla 3: no se puede recibir más de lo pedido
                if ($cantidad > $detalle->pendiente()) {
                    throw ValidationException::withMessages([
                        'items' => "No se puede recibir más de lo pedido para «{$detalle->ficha?->denominacion}». "
                            ."Pendiente: {$detalle->pendiente()}, recibido ahora: {$cantidad}.",
                    ]);
                }

                $detalle->increment('cantidad_recibida', $cantidad);

                Ficha::withTrashed()
                    ->lockForUpdate()
                    ->findOrFail($detalle->ficha_id)
                    ->increment('stock', $cantidad);

                $recibioAlgo = true;
            }

            // Regla 4: una recepción debe traer al menos una unidad
            if (! $recibioAlgo) {
                throw ValidationException::withMessages([
                    'items' => 'Debe ingresar al menos una cantidad mayor que cero para registrar la recepción.',
                ]);
            }

            // Recalcular el estado de la orden
            $compra->load('detalles');
            $completa = $compra->detalles->every(fn ($d) => $d->cantidad_recibida >= $d->cantidad);

            $compra->update([
                'estado' => $completa ? 'recibida' : 'parcial',
                'recibida_en' => $completa ? now() : null,
                'factura_proveedor' => $facturaProveedor ?: $compra->factura_proveedor,
            ]);

            return $compra;
        });
    }

    /**
     * Anula una orden. Regla: solo si NO ha llegado nada (estado "pendiente").
     */
    public function anular(Compra $compra): void
    {
        if (! $compra->puedeAnularse()) {
            throw ValidationException::withMessages([
                'compra' => "La orden {$compra->numero_compra} ya tiene fichas recibidas y no se puede anular.",
            ]);
        }

        DB::transaction(function () use ($compra) {
            $compra->update(['estado' => 'anulada']);
            $compra->delete();
        });
    }

    // OC-00001, OC-00002, ...
    private function siguienteNumero(): string
    {
        $ultima = Compra::withTrashed()->orderByDesc('id')->first();
        $siguiente = $ultima ? $ultima->id + 1 : 1;

        return 'OC-'.str_pad($siguiente, 5, '0', STR_PAD_LEFT);
    }
}