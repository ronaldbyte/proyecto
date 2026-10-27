<?php

namespace App\Models;

use App\Soporte\Constantes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Solicitud extends Model
{
    protected $table = 'solicitudes';

    protected $fillable = [
        'docente_id',
        'fecha_inicio',
        'fecha_fin',
        'jornada',
        'dias_solicitados',
        'motivo',
        'estado',
        'cancelada_por',
        'cancelada_en',
        'motivo_cancelacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'dias_solicitados' => 'float',
            'cancelada_en' => 'datetime',
        ];
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }

    public function cancelador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'cancelada_por');
    }

    public function aprobaciones(): HasMany
    {
        return $this->hasMany(Aprobacion::class, 'solicitud_id')->orderBy('created_at');
    }

    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class, 'solicitud_id');
    }

    public function aprobacionDirectorCarrera(): ?Aprobacion
    {
        return $this->aprobaciones->firstWhere('etapa', 'director_carrera');
    }

    public function aprobacionDirectorArea(): ?Aprobacion
    {
        return $this->aprobaciones->firstWhere('etapa', 'director_area');
    }

    public function aprobacionDirector(): ?Aprobacion
    {
        return $this->aprobacionDirectorCarrera()
            ?? $this->aprobacionDirectorArea()
            ?? $this->aprobaciones->firstWhere('etapa', 'director');
    }

    public function aprobacionJefatura(): ?Aprobacion
    {
        return $this->aprobaciones->firstWhere('etapa', 'jefatura');
    }

    public function primeraAprobacion(): ?Aprobacion
    {
        return $this->aprobaciones->first();
    }

    public function textoDecisionCarrera(): string
    {
        return $this->textoDecision(
            $this->aprobacionDirectorCarrera()
                ?? $this->aprobacionDirector()
                ?? $this->primeraAprobacion()
        );
    }

    public function textoDecisionArea(): string
    {
        return $this->textoDecision(
            $this->aprobacionDirectorArea()
                ?? $this->aprobacionDirector()
                ?? $this->primeraAprobacion()
        );
    }

    public function textoDecisionJefatura(): string
    {
        $aprobacion = $this->aprobacionJefatura() ?? $this->primeraAprobacion();
        $texto = $this->textoDecision($aprobacion);

        if ($aprobacion && $aprobacion->etapa === 'jefatura') {
            $texto .= ' '.$aprobacion->created_at->format('d/m/Y H:i');
        }

        return $texto;
    }

    private function textoDecision(?Aprobacion $aprobacion): string
    {
        if (! $aprobacion) {
            return 'Pendiente';
        }

        return $aprobacion->usuario->nombreVisible().' ('.$aprobacion->decision.')';
    }

    public function esMedioDia(): bool
    {
        return Constantes::esMedioDia($this->jornada ?: Constantes::JORNADA_COMPLETA);
    }

    public function etiquetaJornada(): string
    {
        return Constantes::etiquetaJornada($this->jornada ?: Constantes::JORNADA_COMPLETA);
    }

    public function textoDias(): string
    {
        $dias = fmod((float) $this->dias_solicitados, 1.0) === 0.0
            ? (string) (int) $this->dias_solicitados
            : number_format((float) $this->dias_solicitados, 1, '.', '');

        if ($this->esMedioDia()) {
            return $dias.' ('.$this->etiquetaJornada().')';
        }

        return $dias;
    }

    public function etiquetaEstado(): string
    {
        return Constantes::etiquetaEstado($this->estado);
    }

    public function claseEstado(): string
    {
        return Constantes::claseEstado($this->estado);
    }

    public function estaPendiente(): bool
    {
        return $this->estado === Constantes::ESTADO_PENDIENTE;
    }

    public function estaPendienteArea(): bool
    {
        return $this->estado === Constantes::ESTADO_PENDIENTE_AREA;
    }

    public function estaPendienteJefatura(): bool
    {
        return $this->estado === Constantes::ESTADO_PENDIENTE_JEFATURA;
    }

    public function puedeCancelarsePor(Usuario $usuario): bool
    {
        if (! $usuario->docente || $this->docente_id !== $usuario->docente->id) {
            return false;
        }

        $solicitante = $this->docente->usuario;

        // Director de área: su solicitud va directo a pendiente_jefatura
        if ($solicitante?->esDirectorArea()) {
            return $this->estaPendienteJefatura();
        }

        // Director de carrera: su solicitud inicia en pendiente_area
        if ($solicitante?->esDirectorCarrera()) {
            return $this->estaPendienteArea();
        }

        // Docente regular: puede cancelar mientras esté pendiente de carrera/área
        return $this->estaPendiente() || $this->estaPendienteArea();
    }
}
