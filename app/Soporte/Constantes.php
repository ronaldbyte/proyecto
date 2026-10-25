<?php

namespace App\Soporte;

class Constantes
{
    public const ROL_DOCENTE = 'docente';
    public const ROL_DIRECTOR_CARRERA = 'director_carrera';
    public const ROL_DIRECTOR_AREA = 'director_area';
    public const ROL_JEFATURA = 'jefatura_personal';

    public const CONTRATO_TITULAR = 'titular';
    public const CONTRATO_CONTRATADO = 'contratado';
    public const CONTRATO_INTERINO = 'interino';
    public const CONTRATO_CONSULTOR = 'consultor';

    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_PENDIENTE_AREA = 'pendiente_area';
    public const ESTADO_PENDIENTE_JEFATURA = 'pendiente_jefatura';
    public const ESTADO_APROBADA = 'aprobada';
    public const ESTADO_RECHAZADA = 'rechazada';
    public const ESTADO_CANCELADA = 'cancelada';

    public const JORNADA_COMPLETA = 'completa';
    public const JORNADA_MANANA = 'manana';
    public const JORNADA_TARDE = 'tarde';

    public const MAXIMO_DIAS_VACACIONES = 30;

    public static function tiposContrato(): array
    {
        return [
            self::CONTRATO_TITULAR => 'Titular',
            self::CONTRATO_CONTRATADO => 'Contratado',
            self::CONTRATO_INTERINO => 'Interino',
            self::CONTRATO_CONSULTOR => 'Consultor',
        ];
    }

    public static function etiquetaContrato(string $tipo): string
    {
        return self::tiposContrato()[$tipo] ?? $tipo;
    }

    public static function gozaVacaciones(string $tipo): bool
    {
        return in_array($tipo, [
            self::CONTRATO_TITULAR,
            self::CONTRATO_CONTRATADO,
            self::CONTRATO_INTERINO,
        ], true);
    }

    public static function etiquetaEstado(string $estado): string
    {
        return match ($estado) {
            self::ESTADO_PENDIENTE => 'Pendiente de dirección (carrera o área)',
            self::ESTADO_PENDIENTE_AREA => 'Pendiente de Director de Área',
            self::ESTADO_PENDIENTE_JEFATURA => 'Pendiente de Jefatura',
            self::ESTADO_APROBADA => 'Aprobada',
            self::ESTADO_RECHAZADA => 'Rechazada',
            self::ESTADO_CANCELADA => 'Cancelada',
            default => ucfirst($estado),
        };
    }

    public static function claseEstado(string $estado): string
    {
        return match ($estado) {
            self::ESTADO_PENDIENTE => 'estado-pendiente',
            self::ESTADO_PENDIENTE_AREA => 'estado-area',
            self::ESTADO_PENDIENTE_JEFATURA => 'estado-jefatura',
            self::ESTADO_APROBADA => 'estado-aprobada',
            self::ESTADO_RECHAZADA => 'estado-rechazada',
            self::ESTADO_CANCELADA => 'estado-cancelada',
            default => 'estado-pendiente',
        };
    }

    public static function estadosEnCurso(): array
    {
        return [
            self::ESTADO_PENDIENTE,
            self::ESTADO_PENDIENTE_AREA,
            self::ESTADO_PENDIENTE_JEFATURA,
            self::ESTADO_APROBADA,
        ];
    }

    public static function jornadas(): array
    {
        return [
            self::JORNADA_COMPLETA => 'Día(s) completo(s)',
            self::JORNADA_MANANA => 'Solo la mañana',
            self::JORNADA_TARDE => 'Solo la tarde',
        ];
    }

    public static function etiquetaJornada(string $jornada): string
    {
        return self::jornadas()[$jornada] ?? 'Día(s) completo(s)';
    }

    public static function esMedioDia(string $jornada): bool
    {
        return in_array($jornada, [self::JORNADA_MANANA, self::JORNADA_TARDE], true);
    }
}
