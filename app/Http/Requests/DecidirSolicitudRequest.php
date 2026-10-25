<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DecidirSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $reglas = [
            'decision' => ['required', 'in:aprobada,rechazada'],
        ];

        if ($this->user()?->esJefatura()) {
            $reglas['observacion'] = ['nullable', 'string', 'max:2000'];
        }

        return $reglas;
    }

    public function messages(): array
    {
        return [
            'decision.required' => 'Debe indicar si aprueba o rechaza.',
            'decision.in' => 'La decisión no es válida.',
        ];
    }
}
