<?php

namespace App\Models;

use App\Soporte\Constantes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use RuntimeException;

class Usuario extends Authenticatable
{
    protected $table = 'usuarios';

    protected $fillable = [
        'ci',
        'codigo',
        'email',
        'foto',
        'contrasena',
        'rol_id',
        'activo',
        'debe_cambiar_contrasena',
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'contrasena' => 'hashed',
            'activo' => 'boolean',
            'debe_cambiar_contrasena' => 'boolean',
        ];
    }

    public function getAuthPassword(): string
    {
        return $this->contrasena;
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function docente(): HasOne
    {
        return $this->hasOne(Docente::class, 'usuario_id');
    }

    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class, 'usuario_id');
    }

    public static function generarCodigoUnico(?string $rolCodigo = null): string
    {
        $prefijo = match ($rolCodigo) {
            Constantes::ROL_DIRECTOR_CARRERA => 'DRC',
            Constantes::ROL_DIRECTOR_AREA => 'DRA',
            Constantes::ROL_JEFATURA => 'JEF',
            default => 'DOC',
        };

        $alfabeto = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        for ($intento = 0; $intento < 40; $intento++) {
            $sufijo = '';
            for ($i = 0; $i < 4; $i++) {
                $sufijo .= $alfabeto[random_int(0, strlen($alfabeto) - 1)];
            }

            $codigo = $prefijo.$sufijo;

            if (! static::where('codigo', $codigo)->exists()) {
                return $codigo;
            }
        }

        throw new RuntimeException('No se pudo generar un código de ingreso único.');
    }

    public function codigoRol(): ?string
    {
        return $this->rol?->codigo;
    }

    public function esDocente(): bool
    {
        return $this->codigoRol() === Constantes::ROL_DOCENTE;
    }

    public function esDirectorCarrera(): bool
    {
        return $this->codigoRol() === Constantes::ROL_DIRECTOR_CARRERA;
    }

    public function esDirectorArea(): bool
    {
        return $this->codigoRol() === Constantes::ROL_DIRECTOR_AREA;
    }

    public function esJefatura(): bool
    {
        return $this->codigoRol() === Constantes::ROL_JEFATURA;
    }

    public function esDirector(): bool
    {
        return $this->esDirectorCarrera() || $this->esDirectorArea();
    }

    public function puedeSolicitarLicencia(): bool
    {
        return $this->activo
            && ! $this->esJefatura()
            && $this->docente !== null
            && $this->docente->gozaVacaciones();
    }

    public function nombreVisible(): string
    {
        return $this->docente?->nombre_completo ?? $this->codigo;
    }

    public function notificacionesNoLeidas()
    {
        return $this->notificaciones()->where('leida', false);
    }
}
