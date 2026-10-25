<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJuegoRequest extends FormRequest
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
            'nombre' => [
                'required', 'string', 'max:100',
                // Nombre único entre los juegos que no están en la papelera
                Rule::unique('juegos', 'nombre')->withoutTrashed()->ignore($this->route('juego')),
            ],
            'apuesta_min' => ['required', 'numeric', 'min:0'],
            'apuesta_max' => ['required', 'numeric', 'gte:apuesta_min'],
            'activo' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'apuesta_min' => 'apuesta mínima',
            'apuesta_max' => 'apuesta máxima',
        ];
    }
}