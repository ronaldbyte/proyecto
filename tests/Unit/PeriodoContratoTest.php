<?php

namespace Tests\Unit;

use App\Services\ServicioVacaciones;
use Carbon\Carbon;
use Tests\TestCase;

class PeriodoContratoTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_si_ingresa_en_marzo_el_periodo_vence_en_marzo_del_siguiente_anio(): void
    {
        Carbon::setTestNow('2026-08-28');

        [$inicio, $fin] = app(ServicioVacaciones::class)->periodoContrato('2024-03-15');

        $this->assertSame('2026-03-15', $inicio->toDateString());
        $this->assertSame('2027-03-15', $fin->toDateString());
    }

    public function test_antes_del_aniversario_usa_el_periodo_en_curso(): void
    {
        Carbon::setTestNow('2026-02-01');

        [$inicio, $fin] = app(ServicioVacaciones::class)->periodoContrato('2024-03-15');

        $this->assertSame('2025-03-15', $inicio->toDateString());
        $this->assertSame('2026-03-15', $fin->toDateString());
    }

    public function test_el_primer_anio_parte_desde_la_fecha_de_ingreso(): void
    {
        Carbon::setTestNow('2026-08-28');

        [$inicio, $fin] = app(ServicioVacaciones::class)->periodoContrato('2026-03-15');

        $this->assertSame('2026-03-15', $inicio->toDateString());
        $this->assertSame('2027-03-15', $fin->toDateString());
    }
}
