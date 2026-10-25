<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecibirCompraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // items[ID_DEL_DETALLE] = cantidad que llega en esta entrega
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0'],
            'factura_proveedor' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'items.*' => 'cantidad recibida',
            'factura_proveedor' => 'factura del proveedor',
        ];
    }
}