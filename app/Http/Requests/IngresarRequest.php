<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IngresarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:50'],
            'contrasena' => ['required', 'string', 'min:4'],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'Ingrese su código de docente.',
            'contrasena.required' => 'Ingrese su contraseña.',
            'contrasena.min' => 'La contraseña debe tener al menos 4 caracteres.',
        ];
    }
}
