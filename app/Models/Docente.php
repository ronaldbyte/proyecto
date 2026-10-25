<?php

namespace App\Models;

use App\Services\ServicioVacaciones;
use App\Soporte\Constantes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Docente extends Model
{
    protected $table = 'docentes';

    protected $fillable = [
        'usuario_id',
        'nombres',
        'apellidos',
        'nombre_completo',
        'telefono',
        'cargo',
        'tipo_contrato',
        'carrera_id',
        'area_id',
        'fecha_ingreso',
        'antiguedad_anios',
        'dias_asignados',
        'dias_utilizados',
    ];

    protected function casts(): array
    {
        return [
            'fecha_ingreso' => 'date',
            'antiguedad_anios' => 'integer',
            'dias_asignados' => 'integer',
            'dias_utilizados' => 'float',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Docente $docente) {
            $docente->nombre_completo = trim($docente->nombres.' '.$docente->apellidos);
            if ($docente->fecha_ingreso) {
                $docente->antiguedad_anios = $docente->fecha_ingreso->diffInYears(now());
            }

            $vacaciones = app(ServicioVacaciones::class);
            $docente->dias_asignados = $vacaciones->diasAsignadosSegunAntiguedad(
                $docente->tipo_contrato ?: Constantes::CONTRATO_TITULAR,
                (int) $docente->antiguedad_anios
            );

            if (! $docente->exists) {
                $docente->dias_utilizados = 0;
            }
        });
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'docente_id');
    }

    public function gozaVacaciones(): bool
    {
        return Constantes::gozaVacaciones($this->tipo_contrato);
    }

    public function etiquetaContrato(): string
    {
        return Constantes::etiquetaContrato($this->tipo_contrato);
    }

    public function inicioPeriodoContrato(): ?\Carbon\Carbon
    {
        if (! $this->fecha_ingreso) {
            return null;
        }

        return app(ServicioVacaciones::class)->periodoContrato($this->fecha_ingreso->toDateString())[0];
    }

    public function finPeriodoContrato(): ?\Carbon\Carbon
    {
        if (! $this->fecha_ingreso) {
            return null;
        }

        return app(ServicioVacaciones::class)->periodoContrato($this->fecha_ingreso->toDateString())[1];
    }

    public function vigenciaContratoTexto(): ?string
    {
        $inicio = $this->inicioPeriodoContrato();
        $fin = $this->finPeriodoContrato();

        if (! $inicio || ! $fin) {
            return null;
        }

        return $inicio->format('d/m/Y').' al '.$fin->format('d/m/Y');
    }

    public function getDiasDisponiblesAttribute(): float
    {
        return max(0, (float) $this->dias_asignados - (float) $this->dias_utilizados);
    }

    public function getDiasExcedidosAttribute(): float
    {
        return max(0, (float) $this->dias_utilizados - (float) $this->dias_asignados);
    }

    public function tieneDiasExcedidos(): bool
    {
        return $this->dias_excedidos > 0;
    }
}
