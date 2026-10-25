<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['activo' => $this->boolean('activo')]);
    }

    public function rules(): array
    {
        return [
            'tipo_documento' => ['required', Rule::in(['NIT', 'CC', 'CE', 'PP'])],
            'numero_documento' => [
                'required', 'string', 'max:30',
                // Sin withoutTrashed(): la columna es UNIQUE en la BD
                Rule::unique('proveedores', 'numero_documento')->ignore($this->route('proveedor')),
            ],
            'nombre' => ['required', 'string', 'max:150'],
            'contacto' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'activo' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tipo_documento' => 'tipo de documento',
            'numero_documento' => 'número de documento',
            'nombre' => 'nombre',
            'contacto' => 'contacto',
            'email' => 'correo',
            'telefono' => 'teléfono',
            'direccion' => 'dirección',
        ];
    }
}