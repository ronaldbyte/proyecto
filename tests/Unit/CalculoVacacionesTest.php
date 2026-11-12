<?php

namespace Tests\Unit;

use App\Services\ServicioVacaciones;
use App\Soporte\Constantes;
use Carbon\Carbon;
use InvalidArgumentException;
use Tests\TestCase;

class CalculoVacacionesTest extends TestCase
{
    private ServicioVacaciones $servicio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->servicio = app(ServicioVacaciones::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * Camino 1: Jornada de medio día (mañana o tarde) retorna 0.5 días.
     */
    public function test_jornada_medio_dia_retorna_medio_dia_en_manana_y_tarde(): void
    {
        $diasManana = $this->servicio->calcularDiasSolicitados('2026-11-02', '2026-11-02', Constantes::JORNADA_MANANA);
        $diasTarde = $this->servicio->calcularDiasSolicitados('2026-11-02', '2026-11-02', Constantes::JORNADA_TARDE);

        $this->assertSame(0.5, $diasManana);
        $this->assertSame(0.5, $diasTarde);
    }

    /**
     * Camino 2: Jornada completa en rango de fechas normal.
     */
    public function test_jornada_completa_calcula_dias_calendario_correctamente(): void
    {
        // Del lunes 2 de nov al viernes 6 de nov = 5 días
        $dias = $this->servicio->calcularDiasSolicitados('2026-11-02', '2026-11-06', Constantes::JORNADA_COMPLETA);

        $this->assertSame(5.0, $dias);
    }

    /**
     * Camino 3: Caso frontera de un solo día completo (inicio igual a fin).
     */
    public function test_jornada_completa_de_un_solo_dia_retorna_un_dia(): void
    {
        $dias = $this->servicio->calcularDiasSolicitados('2026-11-02', '2026-11-02', Constantes::JORNADA_COMPLETA);

        $this->assertSame(1.0, $dias);
    }

    /**
     * Camino 4: Excepción cuando la fecha final es anterior a la fecha inicial.
     */
    public function test_lanza_excepcion_cuando_fecha_fin_es_anterior_a_fecha_inicio(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('La fecha de finalización no puede ser anterior a la fecha de inicio.');

        $this->servicio->calcularDiasSolicitados('2026-11-10', '2026-11-05', Constantes::JORNADA_COMPLETA);
    }

    /**
     * Camino 5: Antigüedad nula retorna cero.
     */
    public function test_antiguedad_con_fecha_nula_retorna_cero(): void
    {
        $antiguedad = $this->servicio->calcularAntiguedad(null);

        $this->assertSame(0, $antiguedad);
    }

    /**
     * Camino 6: Antigüedad calculada según fecha de ingreso.
     */
    public function test_antiguedad_calcula_anios_correctamente(): void
    {
        Carbon::setTestNow('2026-11-01');

        $antiguedad = $this->servicio->calcularAntiguedad('2020-03-01');

        $this->assertSame(6, $antiguedad);
    }

    /**
     * Camino 7: Contrato sin derecho a vacaciones retorna cero días.
     */
    public function test_contrato_consultor_no_goza_de_vacaciones(): void
    {
        $dias = $this->servicio->diasAsignadosSegunAntiguedad(Constantes::CONTRATO_CONSULTOR, 8);

        $this->assertSame(0, $dias);
    }

    /**
     * Camino 8: Antigüedad menor a 5 años recibe 15 días.
     */
    public function test_antiguedad_menor_a_cinco_anios_asigna_quince_dias(): void
    {
        $dias = $this->servicio->diasAsignadosSegunAntiguedad(Constantes::CONTRATO_TITULAR, 2);

        $this->assertSame(15, $dias);
    }

    /**
     * Camino 9: Antigüedad entre 5 y 9 años recibe 20 días.
     */
    public function test_antiguedad_entre_cinco_y_nueve_anios_asigna_veinte_dias(): void
    {
        $diasFronteraInferior = $this->servicio->diasAsignadosSegunAntiguedad(Constantes::CONTRATO_TITULAR, 5);
        $diasFronteraSuperior = $this->servicio->diasAsignadosSegunAntiguedad(Constantes::CONTRATO_TITULAR, 9);

        $this->assertSame(20, $diasFronteraInferior);
        $this->assertSame(20, $diasFronteraSuperior);
    }

    /**
     * Camino 10: Antigüedad mayor o igual a 10 años recibe 30 días.
     */
    public function test_antiguedad_mayor_o_igual_a_diez_anios_asigna_treinta_dias(): void
    {
        $diasExactos = $this->servicio->diasAsignadosSegunAntiguedad(Constantes::CONTRATO_TITULAR, 10);
        $diasVeterano = $this->servicio->diasAsignadosSegunAntiguedad(Constantes::CONTRATO_TITULAR, 15);

        $this->assertSame(30, $diasExactos);
        $this->assertSame(30, $diasVeterano);
    }

    /**
     * Camino 11: Menos de un año de servicio no asigna días.
     */
    public function test_menos_de_un_anio_de_servicio_no_asigna_dias(): void
    {
        $this->assertSame(0, $this->servicio->diasAsignadosSegunAntiguedad(Constantes::CONTRATO_TITULAR, 0));
    }

    /**
     * Camino 12: Un año y un día de servicio asigna 15 días.
     */
    public function test_un_anio_y_un_dia_de_servicio_asigna_quince_dias(): void
    {
        $this->assertSame(15, $this->servicio->diasAsignadosSegunAntiguedad(Constantes::CONTRATO_TITULAR, 1));
    }

    /**
     * Camino 13: El tramo nuevo empieza un día después del aniversario (1, 5 y 10 años).
     */
    public function test_el_tramo_nuevo_empieza_el_dia_siguiente_al_aniversario(): void
    {
        foreach ([[1, 0, 15], [5, 4, 20], [10, 9, 30]] as [$aniosDeServicio, $aniosEnElAniversario, $diasEsperados]) {
            $ingreso = Carbon::parse('2026-11-01')->subYears($aniosDeServicio)->toDateString();

            Carbon::setTestNow('2026-11-01');
            $enElAniversario = $this->servicio->calcularAntiguedad($ingreso);
            $this->assertSame($aniosEnElAniversario, $enElAniversario, "Antigüedad el día del aniversario de $aniosDeServicio años");

            Carbon::setTestNow('2026-11-02');
            $diaSiguiente = $this->servicio->calcularAntiguedad($ingreso);
            $this->assertSame($diasEsperados, $this->servicio->diasAsignadosSegunAntiguedad(Constantes::CONTRATO_TITULAR, $diaSiguiente));
        }
    }

    /**
     * Camino 14: El día exacto del aniversario todavía rige el tramo anterior.
     */
    public function test_el_dia_exacto_del_aniversario_rige_el_tramo_anterior(): void
    {
        Carbon::setTestNow('2026-11-01');

        $cincoAnios = $this->servicio->calcularAntiguedad('2021-11-01');
        $diezAnios = $this->servicio->calcularAntiguedad('2016-11-01');

        $this->assertSame(15, $this->servicio->diasAsignadosSegunAntiguedad(Constantes::CONTRATO_TITULAR, $cincoAnios));
        $this->assertSame(20, $this->servicio->diasAsignadosSegunAntiguedad(Constantes::CONTRATO_TITULAR, $diezAnios));
    }
}

