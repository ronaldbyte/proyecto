<?php

namespace App\Services;

use Carbon\Carbon;

class ServicioFeriados
{
    /**
     * Determina si una fecha dada es día hábil laboral (lunes a viernes, no feriado).
     */
    public function esDiaHabil(Carbon $fecha): bool
    {
        // 0 = Domingo, 6 = Sábado
        if ($fecha->isWeekend()) {
            return false;
        }

        return ! $this->esFeriado($fecha);
    }

    /**
     * Determina si una fecha dada es feriado oficial en Bolivia (nacional o departamental de Potosí).
     */
    public function esFeriado(Carbon $fecha): bool
    {
        $feriados = $this->obtenerFeriadosDelAnio($fecha->year);
        $fechaStr = $fecha->format('Y-m-d');

        return isset($feriados[$fechaStr]);
    }

    /**
     * Obtiene el nombre del feriado si la fecha es feriado, o null si no lo es.
     */
    public function nombreFeriado(Carbon $fecha): ?string
    {
        $feriados = $this->obtenerFeriadosDelAnio($fecha->year);
        $fechaStr = $fecha->format('Y-m-d');

        return $feriados[$fechaStr] ?? null;
    }

    /**
     * Calcula la lista completa de feriados de Bolivia para un año dado,
     * incluyendo feriados fijos, móviles y el traslado de domingo a lunes (D.S. 2750).
     *
     * @return array<string, string> Formato ['YYYY-MM-DD' => 'Nombre del Feriado']
     */
    public function obtenerFeriadosDelAnio(int $anio): array
    {
        $feriados = [];

        // 1. Feriados Fijos Nacionales y Departamental de Potosí (10 de Noviembre)
        $fijos = [
            '01-01' => 'Año Nuevo',
            '01-22' => 'Día del Estado Plurinacional de Bolivia',
            '05-01' => 'Día del Trabajo',
            '06-21' => 'Año Nuevo Andino Amazónico Chaqueño (Willkakuti)',
            '08-06' => 'Día de la Independencia de Bolivia',
            '11-02' => 'Día de Todos los Santos',
            '11-10' => 'Aniversario Cívico de Potosí',
            '12-25' => 'Navidad',
        ];

        foreach ($fijos as $mesDia => $nombre) {
            $fecha = Carbon::createFromFormat('Y-m-d', "{$anio}-{$mesDia}")->startOfDay();
            $feriados[$fecha->format('Y-m-d')] = $nombre;

            // Regla de traslado: Si el feriado fijo cae en domingo, se traslada al lunes
            if ($fecha->isSunday()) {
                $lunes = $fecha->copy()->addDay();
                $feriados[$lunes->format('Y-m-d')] = "{$nombre} (Traslado por domingo)";
            }
        }

        // 2. Feriados Móviles religiosos (basados en el Domingo de Resurrección / Pascua)
        $pascua = $this->calcularDomingoPascua($anio);

        // Lunes y Martes de Carnaval (-48 y -47 días antes de Pascua)
        $lunesCarnaval = $pascua->copy()->subDays(48);
        $martesCarnaval = $pascua->copy()->subDays(47);
        $feriados[$lunesCarnaval->format('Y-m-d')] = 'Lunes de Carnaval';
        $feriados[$martesCarnaval->format('Y-m-d')] = 'Martes de Carnaval';

        // Viernes Santo (-2 días antes de Pascua)
        $viernesSanto = $pascua->copy()->subDays(2);
        $feriados[$viernesSanto->format('Y-m-d')] = 'Viernes Santo';

        // Corpus Christi (+60 días después de Pascua, jueves)
        $corpusChristi = $pascua->copy()->addDays(60);
        $feriados[$corpusChristi->format('Y-m-d')] = 'Corpus Christi';

        return $feriados;
    }

    /**
     * Calcula la fecha del Domingo de Pascua para un año determinado
     * utilizando el algoritmo canónico de Butcher / Meeus.
     */
    public function calcularDomingoPascua(int $anio): Carbon
    {
        $a = $anio % 19;
        $b = intdiv($anio, 100);
        $c = $anio % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $mes = intdiv($h + $l - 7 * $m + 114, 31);
        $dia = (($h + $l - 7 * $m + 114) % 31) + 1;

        return Carbon::createFromDate($anio, $mes, $dia)->startOfDay();
    }

    /**
     * Suma una cantidad de días hábiles a una fecha de inicio,
     * saltando fines de semana y feriados de Bolivia, y devuelve la fecha del primer día de trabajo al retornar.
     */
    public function calcularRetornoVacaciones(Carbon $fechaInicio, int $diasVacacion): array
    {
        $cursor = $fechaInicio->copy()->startOfDay();

        // Si la fecha seleccionada cae en fin de semana o feriado, avanzamos al primer día hábil
        while (! $this->esDiaHabil($cursor)) {
            $cursor->addDay();
        }

        $inicioEfectivo = $cursor->copy();

        if ($diasVacacion <= 0) {
            return [
                'inicio' => $inicioEfectivo,
                'retorno' => $inicioEfectivo,
            ];
        }

        // Consumir los días hábiles de vacación
        $diasConsumidos = 0;
        while ($diasConsumidos < $diasVacacion) {
            if ($this->esDiaHabil($cursor)) {
                $diasConsumidos++;
                if ($diasConsumidos === $diasVacacion) {
                    break;
                }
            }
            $cursor->addDay();
        }

        // El retorno es el primer día hábil posterior al último día de vacación
        $cursor->addDay();
        while (! $this->esDiaHabil($cursor)) {
            $cursor->addDay();
        }

        return [
            'inicio' => $inicioEfectivo,
            'retorno' => $cursor->copy(),
        ];
    }
}
