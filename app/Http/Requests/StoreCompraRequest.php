<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Regla propia del casino: no se compra a un proveedor inactivo
            'proveedor_id' => ['required', Rule::exists('proveedores', 'id')->whereNull('deleted_at')->where('activo', true)],
            'fecha_compra' => ['required', 'date', 'before_or_equal:today'],
            'factura_proveedor' => ['nullable', 'string', 'max:50'],
            'notas' => ['nullable', 'string', 'max:500'],

            'items' => ['required', 'array', 'min:1'],
            // distinct: la misma ficha no puede aparecer en dos filas; solo fichas activas
            'items.*.ficha_id' => ['required', 'distinct', Rule::exists('fichas', 'id')->whereNull('deleted_at')->where('activo', true)],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
            'items.*.costo_unitario' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function attributes(): array
    {
        return [
            'proveedor_id' => 'proveedor',
            'fecha_compra' => 'fecha de compra',
            'factura_proveedor' => 'factura del proveedor',
            'items' => 'fichas',
            'items.*.ficha_id' => 'ficha',
            'items.*.cantidad' => 'cantidad',
            'items.*.costo_unitario' => 'costo unitario',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Debe agregar al menos una ficha a la orden de compra.',
            'items.min' => 'Debe agregar al menos una ficha a la orden de compra.',
            'items.*.ficha_id.distinct' => 'Hay fichas repetidas: use una sola fila por ficha.',
            'proveedor_id.exists' => 'El proveedor seleccionado no existe o está inactivo.',
        ];
    }
}