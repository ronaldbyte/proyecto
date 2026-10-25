<?php

namespace App\Http\Requests;

use App\Soporte\Constantes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardarSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->puedeSolicitarLicencia() === true;
    }

    protected function prepareForValidation(): void
    {
        $jornada = $this->input('jornada', Constantes::JORNADA_COMPLETA);

        if (Constantes::esMedioDia($jornada)) {
            $this->merge([
                'fecha_fin' => $this->input('fecha_inicio'),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'fecha_inicio' => ['required', 'date', 'after_or_equal:today'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'jornada' => ['nullable', Rule::in(array_keys(Constantes::jornadas()))],
            'motivo' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_inicio.required' => 'Indique la fecha de inicio.',
            'fecha_inicio.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'fecha_fin.required' => 'Indique la fecha de finalización.',
            'fecha_fin.after_or_equal' => 'La fecha final no puede ser anterior a la fecha de inicio.',
            'jornada.in' => 'Elija si pide el día completo, solo la mañana o solo la tarde.',
            'motivo.required' => 'Describa el motivo de la solicitud.',
            'motivo.min' => 'El motivo debe tener al menos 10 caracteres.',
        ];
    }
}
