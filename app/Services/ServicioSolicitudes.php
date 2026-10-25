<?php

namespace App\Services;

use App\Models\Solicitud;
use App\Models\Usuario;
use App\Soporte\Constantes;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ServicioSolicitudes
{
    public function __construct(
        private ServicioVacaciones $vacaciones,
        private ServicioNotificaciones $notificaciones,
        private ServicioAuditoria $auditoria
    ) {}

    public function crear(Usuario $usuario, array $datos): Solicitud
    {
        if (! $usuario->puedeSolicitarLicencia()) {
            throw new InvalidArgumentException('El usuario no puede solicitar una licencia.');
        }

        $docente = $usuario->docente;
        $jornada = $datos['jornada'] ?? Constantes::JORNADA_COMPLETA;
        $fechaInicio = $datos['fecha_inicio'];
        $fechaFin = Constantes::esMedioDia($jornada) ? $fechaInicio : $datos['fecha_fin'];
        $dias = $this->vacaciones->calcularDiasSolicitados($fechaInicio, $fechaFin, $jornada);
        $estadoInicial = $this->estadoInicial($usuario);

        $this->validarSolape($docente->id, $fechaInicio, $fechaFin, $jornada);

        return DB::transaction(function () use ($usuario, $docente, $datos, $dias, $estadoInicial, $jornada, $fechaInicio, $fechaFin) {
            $solicitud = Solicitud::create([
                'docente_id' => $docente->id,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'jornada' => $jornada,
                'dias_solicitados' => $dias,
                'motivo' => $datos['motivo'],
                'estado' => $estadoInicial,
            ]);

            $this->auditoria->registrar(
                'crear_solicitud',
                'solicitud',
                $solicitud->id,
                null,
                $solicitud->toArray(),
                $usuario
            );

            $this->notificaciones->notificarNuevaSolicitud($solicitud);

            return $solicitud;
        });
    }

    public function estadoInicial(Usuario $usuario): string
    {
        if ($usuario->esDirectorArea()) {
            return Constantes::ESTADO_PENDIENTE_JEFATURA;
        }

        if ($usuario->esDirectorCarrera()) {
            return Constantes::ESTADO_PENDIENTE_AREA;
        }

        return Constantes::ESTADO_PENDIENTE;
    }

    public function cancelar(Solicitud $solicitud, Usuario $usuario, string $motivo): void
    {
        if (! $solicitud->puedeCancelarsePor($usuario)) {
            throw new InvalidArgumentException('Esta solicitud no puede cancelarse.');
        }

        $anterior = $solicitud->estado;

        DB::transaction(function () use ($solicitud, $usuario, $anterior, $motivo) {
            $solicitud->update([
                'estado' => Constantes::ESTADO_CANCELADA,
                'cancelada_por' => $usuario->id,
                'cancelada_en' => now(),
                'motivo_cancelacion' => $motivo,
            ]);

            $this->auditoria->registrar(
                'cancelar_solicitud',
                'solicitud',
                $solicitud->id,
                ['estado' => $anterior],
                ['estado' => Constantes::ESTADO_CANCELADA],
                $usuario
            );
        });
    }

    public function validarSolape(int $docenteId, string $inicio, string $fin, string $jornada = Constantes::JORNADA_COMPLETA, ?int $exceptoId = null): void
    {
        $existentes = Solicitud::query()
            ->where('docente_id', $docenteId)
            ->when($exceptoId, fn ($q) => $q->where('id', '!=', $exceptoId))
            ->whereIn('estado', Constantes::estadosEnCurso())
            ->whereDate('fecha_inicio', '<=', $fin)
            ->whereDate('fecha_fin', '>=', $inicio)
            ->get();

        foreach ($existentes as $otra) {
            if ($this->jornadasChocan($jornada, $otra->jornada ?: Constantes::JORNADA_COMPLETA)) {
                throw new InvalidArgumentException('Ya existe una solicitud en esas fechas que aún no ha sido rechazada o cancelada.');
            }
        }
    }

    private function jornadasChocan(string $nueva, string $existente): bool
    {
        if ($nueva === Constantes::JORNADA_COMPLETA || $existente === Constantes::JORNADA_COMPLETA) {
            return true;
        }

        return $nueva === $existente;
    }
}
