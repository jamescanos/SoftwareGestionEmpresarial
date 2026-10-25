<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['required', Rule::exists('clientes', 'id')->whereNull('deleted_at')],
            'fecha_venta' => ['required', 'date'],
            'notas' => ['nullable', 'string', 'max:500'],

            // Detalle de la venta
            'items' => ['required', 'array', 'min:1'],
            'items.*.ficha_id' => ['required', Rule::exists('fichas', 'id')->whereNull('deleted_at')->where('activo', true)],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'fecha_venta' => 'fecha de venta',
            'items' => 'fichas',
            'items.*.ficha_id' => 'ficha',
            'items.*.cantidad' => 'cantidad',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Debe agregar al menos una ficha a la venta.',
            'items.min' => 'Debe agregar al menos una ficha a la venta.',
        ];
    }
}