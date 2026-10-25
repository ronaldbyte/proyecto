<?php

namespace App\Services;

use App\Models\Aprobacion;
use App\Models\Docente;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Soporte\Constantes;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ServicioAprobaciones
{
    public function __construct(
        private ServicioVacaciones $vacaciones,
        private ServicioNotificaciones $notificaciones,
        private ServicioAuditoria $auditoria
    ) {}

    public function puedeRevisarComoDirector(Usuario $usuario, Solicitud $solicitud): bool
    {
        if (! $usuario->esDirector() || ! $usuario->activo || ! $usuario->docente) {
            return false;
        }

        if ($solicitud->docente->usuario_id === $usuario->id) {
            return false;
        }

        if ($usuario->esDirectorCarrera()) {
            return $solicitud->estaPendiente()
                && $solicitud->docente->carrera_id === $usuario->docente->carrera_id;
        }

        if ($usuario->esDirectorArea()) {
            return ($solicitud->estaPendiente() || $solicitud->estaPendienteArea())
                && $solicitud->docente->area_id === $usuario->docente->area_id;
        }

        return false;
    }

    public function estadoPendienteDe(Usuario $usuario): ?string
    {
        return $this->estadosPendientesDe($usuario)[0] ?? null;
    }

    public function estadosPendientesDe(Usuario $usuario): array
    {
        if ($usuario->esDirectorCarrera()) {
            return [Constantes::ESTADO_PENDIENTE];
        }

        if ($usuario->esDirectorArea()) {
            return [Constantes::ESTADO_PENDIENTE, Constantes::ESTADO_PENDIENTE_AREA];
        }

        return [];
    }

    public function decidirComoDirector(Solicitud $solicitud, Usuario $usuario, string $decision): void
    {
        if (! $this->puedeRevisarComoDirector($usuario, $solicitud)) {
            throw new InvalidArgumentException('No tiene autorización para revisar esta solicitud.');
        }

        if (! in_array($decision, ['aprobada', 'rechazada'], true)) {
            throw new InvalidArgumentException('La decisión no es válida.');
        }

        DB::transaction(function () use ($solicitud, $usuario, $decision) {
            $solicitud = Solicitud::lockForUpdate()->findOrFail($solicitud->id);

            if (! $this->puedeRevisarComoDirector($usuario, $solicitud)) {
                throw new InvalidArgumentException('La solicitud ya fue atendida.');
            }

            $estadoAnterior = $solicitud->estado;
            $etapa = $usuario->esDirectorCarrera() ? 'director_carrera' : 'director_area';

            $estadoNuevo = $decision === 'rechazada'
                ? Constantes::ESTADO_RECHAZADA
                : Constantes::ESTADO_PENDIENTE_JEFATURA;

            $solicitud->update(['estado' => $estadoNuevo]);

            Aprobacion::create([
                'solicitud_id' => $solicitud->id,
                'usuario_id' => $usuario->id,
                'rol_codigo' => $usuario->codigoRol(),
                'etapa' => $etapa,
                'decision' => $decision,
                'observacion' => null,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $estadoNuevo,
            ]);

            $this->auditoria->registrar(
                'decision_director',
                'solicitud',
                $solicitud->id,
                ['estado' => $estadoAnterior],
                ['estado' => $estadoNuevo, 'decision' => $decision],
                $usuario
            );

            if ($decision === 'rechazada') {
                $this->notificaciones->notificarRechazada($solicitud);
            } else {
                $this->notificaciones->notificarPendienteJefatura($solicitud);
            }
        });
    }

    public function decidirComoJefatura(Solicitud $solicitud, Usuario $usuario, string $decision, string $observacion = ''): void
    {
        if (! $usuario->esJefatura()) {
            throw new InvalidArgumentException('Solo Jefatura de Personal puede tomar la decisión final.');
        }

        if (! $solicitud->estaPendienteJefatura()) {
            throw new InvalidArgumentException('La solicitud no está pendiente de Jefatura.');
        }

        if (! in_array($decision, ['aprobada', 'rechazada'], true)) {
            throw new InvalidArgumentException('La decisión no es válida.');
        }

        DB::transaction(function () use ($solicitud, $usuario, $decision, $observacion) {
            $solicitud = Solicitud::lockForUpdate()->findOrFail($solicitud->id);

            if (! $solicitud->estaPendienteJefatura()) {
                throw new InvalidArgumentException('La solicitud ya fue atendida.');
            }

            $estadoAnterior = $solicitud->estado;
            $estadoNuevo = $decision === 'aprobada'
                ? Constantes::ESTADO_APROBADA
                : Constantes::ESTADO_RECHAZADA;

            $solicitud->update(['estado' => $estadoNuevo]);

            if ($decision === 'aprobada') {
                $docente = Docente::lockForUpdate()->findOrFail($solicitud->docente_id);
                $this->vacaciones->actualizarDiasUtilizados($docente);
            }

            Aprobacion::create([
                'solicitud_id' => $solicitud->id,
                'usuario_id' => $usuario->id,
                'rol_codigo' => $usuario->codigoRol(),
                'etapa' => 'jefatura',
                'decision' => $decision,
                'observacion' => $observacion ?: null,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $estadoNuevo,
            ]);

            $this->auditoria->registrar(
                'decision_jefatura',
                'solicitud',
                $solicitud->id,
                ['estado' => $estadoAnterior],
                ['estado' => $estadoNuevo, 'decision' => $decision],
                $usuario
            );

            if ($decision === 'aprobada') {
                $this->notificaciones->notificarAprobada($solicitud);
            } else {
                $this->notificaciones->notificarRechazada($solicitud, $observacion);
            }
        });
    }
}
