<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'documento' => [
                'required', 'string', 'max:20',
                // Sin withoutTrashed(): la columna es UNIQUE en la BD, así que también cuenta a los clientes en la papelera
                Rule::unique('clientes', 'documento')->ignore($this->route('cliente')),
            ],
            'nombre' => ['required', 'string', 'max:150'],
            // Regla del casino: solo mayores de 18 años
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:'.now()->subYears(18)->toDateString()],
            'nivel_vip' => ['required', Rule::in(['estandar', 'oro', 'platino'])],
            'saldo_fichas' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
        ];
    }

    public function attributes(): array
    {
        return [
            'documento' => 'documento',
            'nombre' => 'nombre',
            'fecha_nacimiento' => 'fecha de nacimiento',
            'nivel_vip' => 'nivel VIP',
            'saldo_fichas' => 'saldo de fichas',
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_nacimiento.before_or_equal' => 'El cliente debe ser mayor de 18 años.',
        ];
    }
}