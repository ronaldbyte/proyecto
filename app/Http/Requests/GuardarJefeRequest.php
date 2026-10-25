<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardarJefeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esJefatura() === true;
    }

    public function rules(): array
    {
        $usuarioId = $this->route('docente')?->usuario_id;

        return [
            'ci' => [
                'required',
                'string',
                'max:20',
                Rule::unique('usuarios', 'ci')->ignore($usuarioId),
            ],
            'nombres' => ['required', 'string', 'max:120'],
            'apellidos' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:40'],
            'cargo' => ['required', 'string', 'max:120'],
            'contrasena' => ['nullable', 'string', 'min:5'],
            'activo' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'ci.required' => 'El carnet de identidad (CI) es obligatorio.',
            'nombres.required' => 'El nombre es obligatorio.',
            'apellidos.required' => 'El apellido es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'cargo.required' => 'El cargo es obligatorio.',
            'contrasena.min' => 'La contraseña debe tener al menos 5 caracteres.',
        ];
    }
}
