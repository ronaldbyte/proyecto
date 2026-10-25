<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardarDocenteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esJefatura() === true;
    }

    public function rules(): array
    {
        // Al editar, obtener el ID del usuario actual para ignorarlo en la unicidad del CI
        $docenteId = $this->route('docente')?->id;
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
            'tipo_contrato' => ['required', 'in:titular,contratado,interino,consultor'],
            'carrera_id' => ['required', 'exists:carreras,id'],
            'rol_id' => ['required', 'exists:roles,id'],
            'fecha_ingreso' => ['required', 'date', 'before_or_equal:today'],
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
            'carrera_id.required' => 'Debe seleccionar una carrera.',
            'rol_id.required' => 'Debe seleccionar un rol.',
            'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria.',
            'tipo_contrato.required' => 'Debe indicar el tipo de contrato.',
            'tipo_contrato.in' => 'El tipo de contrato no es válido.',
            'contrasena.min' => 'La contraseña debe tener al menos 5 caracteres.',
        ];
    }
}
