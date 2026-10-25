<?php

namespace App\Services;

use App\Models\Docente;
use App\Soporte\Constantes;
use Carbon\Carbon;
use InvalidArgumentException;

class ServicioVacaciones
{
    public function calcularDiasSolicitados(string $fechaInicio, string $fechaFin, string $jornada = Constantes::JORNADA_COMPLETA): float
    {
        if (Constantes::esMedioDia($jornada)) {
            return 0.5;
        }

        $inicio = Carbon::parse($fechaInicio)->startOfDay();
        $fin = Carbon::parse($fechaFin)->startOfDay();

        if ($fin->lt($inicio)) {
            throw new InvalidArgumentException('La fecha de finalización no puede ser anterior a la fecha de inicio.');
        }

        return (float) ($inicio->diffInDays($fin) + 1);
    }

    public function calcularAntiguedad(?string $fechaIngreso): int
    {
        if (! $fechaIngreso) {
            return 0;
        }

        return Carbon::parse($fechaIngreso)->diffInYears(now());
    }

    public function diasAsignadosSegunAntiguedad(string $tipoContrato, int $anios): int
    {
        if (! Constantes::gozaVacaciones($tipoContrato)) {
            return 0;
        }

        if ($anios > 10) {
            return 30;
        }

        if ($anios >= 5) {
            return 20;
        }

        return 15;
    }

    public function periodoContrato(?string $fechaIngreso): array
    {
        $ingreso = Carbon::parse($fechaIngreso)->startOfDay();
        $hoy = now()->startOfDay();
        $aniversario = $ingreso->copy()->year($hoy->year);

        if ($aniversario->gt($hoy)) {
            $inicio = $aniversario->copy()->subYear();
            $fin = $aniversario->copy();
        } else {
            $inicio = $aniversario->copy();
            $fin = $aniversario->copy()->addYear();
        }

        if ($inicio->lt($ingreso)) {
            $inicio = $ingreso->copy();
            $fin = $ingreso->copy()->addYear();
        }

        return [$inicio, $fin];
    }

    public function diasUtilizadosEnPeriodo(Docente $docente): float
    {
        if (! $docente->id || ! $docente->fecha_ingreso) {
            return 0;
        }

        [$inicio, $fin] = $this->periodoContrato($docente->fecha_ingreso->toDateString());

        return (float) $docente->solicitudes()
            ->where('estado', Constantes::ESTADO_APROBADA)
            ->whereDate('fecha_inicio', '>=', $inicio)
            ->whereDate('fecha_inicio', '<', $fin)
            ->sum('dias_solicitados');
    }

    public function actualizarDiasUtilizados(Docente $docente): void
    {
        $docente->dias_utilizados = $docente->gozaVacaciones()
            ? $this->diasUtilizadosEnPeriodo($docente)
            : 0;
        $docente->saveQuietly();
    }
}
