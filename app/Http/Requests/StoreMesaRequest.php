<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMesaRequest extends FormRequest
{
    // Los permisos se validan en el controlador (middleware de Spatie)
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'juego_id' => ['required', Rule::exists('juegos', 'id')->whereNull('deleted_at')],
            'estado' => ['required', Rule::in(['abierta', 'cerrada'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'juego_id' => 'juego',
            'estado' => 'estado',
        ];
    }
}