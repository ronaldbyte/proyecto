<?php

namespace Tests\Feature;

use App\Models\Docente;
use App\Models\Rol;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Soporte\Constantes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificacionCalculosYNotificacionesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_solicitud_medio_dia_se_guarda_con_medio_dia_exacto(): void
    {
        $docenteUsuario = Usuario::where('codigo', 'DOC001')->firstOrFail();

        // 1. Solicitar medio día en la mañana
        $this->actingAs($docenteUsuario)->post('/solicitudes', [
            'fecha_inicio' => now()->addDays(5)->toDateString(),
            'jornada' => Constantes::JORNADA_MANANA,
            'motivo' => 'Permiso médico en la mañana para consulta.',
        ])->assertRedirect('/solicitudes');

        $solicitudManana = Solicitud::latest('id')->firstOrFail();
        $this->assertEquals(0.5, $solicitudManana->dias_solicitados);
        $this->assertSame(Constantes::JORNADA_MANANA, $solicitudManana->jornada);
        $this->assertSame('Medio día (mañana)', $solicitudManana->textoDias());
        $this->assertTrue($solicitudManana->fecha_inicio->isSameDay($solicitudManana->fecha_fin));

        // 2. Solicitar medio día en la tarde para el día siguiente
        $this->actingAs($docenteUsuario)->post('/solicitudes', [
            'fecha_inicio' => now()->addDays(6)->toDateString(),
            'jornada' => Constantes::JORNADA_TARDE,
            'motivo' => 'Permiso personal en la tarde para trámite.',
        ])->assertRedirect('/solicitudes');

        $solicitudTarde = Solicitud::latest('id')->firstOrFail();
        $this->assertEquals(0.5, $solicitudTarde->dias_solicitados);
        $this->assertSame(Constantes::JORNADA_TARDE, $solicitudTarde->jornada);
        $this->assertSame('Medio día (tarde)', $solicitudTarde->textoDias());
        $this->assertTrue($solicitudTarde->fecha_inicio->isSameDay($solicitudTarde->fecha_fin));
    }

    public function test_aprobacion_de_medios_dias_descuenta_y_guarda_vacaciones_en_tiempo_real(): void
    {
        $docenteUsuario = Usuario::where('codigo', 'DOC001')->firstOrFail();
        $docente = $docenteUsuario->docente;
        $docente->update(['dias_utilizados' => 0]);

        $jefatura = Usuario::where('codigo', 'JEF001')->firstOrFail();

        // Crear y aprobar primer medio día
        $solicitud1 = Solicitud::create([
            'docente_id' => $docente->id,
            'fecha_inicio' => now()->addDays(10)->toDateString(),
            'fecha_fin' => now()->addDays(10)->toDateString(),
            'jornada' => Constantes::JORNADA_MANANA,
            'dias_solicitados' => 0.5,
            'motivo' => 'Medio día mañana aprobado.',
            'estado' => Constantes::ESTADO_PENDIENTE_JEFATURA,
        ]);

        $this->actingAs($jefatura)->post('/jefatura/solicitudes/'.$solicitud1->id.'/decidir', [
            'decision' => 'aprobada',
        ])->assertRedirect('/jefatura/solicitudes/pendientes');

        $this->assertEquals(0.5, $docente->fresh()->dias_utilizados);
        $this->assertEquals($docente->fresh()->dias_asignados - 0.5, $docente->fresh()->dias_disponibles);

        // Crear y aprobar segundo medio día (debe sumar exactamente 1.0 día en total)
        $solicitud2 = Solicitud::create([
            'docente_id' => $docente->id,
            'fecha_inicio' => now()->addDays(12)->toDateString(),
            'fecha_fin' => now()->addDays(12)->toDateString(),
            'jornada' => Constantes::JORNADA_TARDE,
            'dias_solicitados' => 0.5,
            'motivo' => 'Medio día tarde aprobado.',
            'estado' => Constantes::ESTADO_PENDIENTE_JEFATURA,
        ]);

        $this->actingAs($jefatura)->post('/jefatura/solicitudes/'.$solicitud2->id.'/decidir', [
            'decision' => 'aprobada',
        ])->assertRedirect('/jefatura/solicitudes/pendientes');

        $this->assertEquals(1.0, $docente->fresh()->dias_utilizados);
        $this->assertEquals($docente->fresh()->dias_asignados - 1.0, $docente->fresh()->dias_disponibles);

        // Crear y aprobar 3 días completos (debe sumar 4.0 días en total)
        $solicitud3 = Solicitud::create([
            'docente_id' => $docente->id,
            'fecha_inicio' => now()->addDays(15)->toDateString(),
            'fecha_fin' => now()->addDays(17)->toDateString(),
            'jornada' => Constantes::JORNADA_COMPLETA,
            'dias_solicitados' => 3,
            'motivo' => 'Tres días completos aprobados.',
            'estado' => Constantes::ESTADO_PENDIENTE_JEFATURA,
        ]);

        $this->actingAs($jefatura)->post('/jefatura/solicitudes/'.$solicitud3->id.'/decidir', [
            'decision' => 'aprobada',
        ])->assertRedirect('/jefatura/solicitudes/pendientes');

        $this->assertEquals(4.0, $docente->fresh()->dias_utilizados);
        $this->assertEquals($docente->fresh()->dias_asignados - 4.0, $docente->fresh()->dias_disponibles);
    }

    public function test_solicitudes_con_fechas_futuras_se_guardan_y_descuentan_correctamente(): void
    {
        $docenteUsuario = Usuario::where('codigo', 'DOC001')->firstOrFail();
        $docente = $docenteUsuario->docente;
        $docente->update(['dias_utilizados' => 0]);

        $jefatura = Usuario::where('codigo', 'JEF001')->firstOrFail();

        // Solicitud en fecha futura (ej. dentro de 3 meses)
        $solicitudFutura = Solicitud::create([
            'docente_id' => $docente->id,
            'fecha_inicio' => now()->addMonths(3)->toDateString(),
            'fecha_fin' => now()->addMonths(3)->addDays(4)->toDateString(),
            'jornada' => Constantes::JORNADA_COMPLETA,
            'dias_solicitados' => 5,
            'motivo' => 'Vacaciones de receso de fin de año planificadas.',
            'estado' => Constantes::ESTADO_PENDIENTE_JEFATURA,
        ]);

        $this->actingAs($jefatura)->post('/jefatura/solicitudes/'.$solicitudFutura->id.'/decidir', [
            'decision' => 'aprobada',
        ])->assertRedirect('/jefatura/solicitudes/pendientes');

        // Los días deben descontarse en tiempo real sin ser ignorados
        $this->assertEquals(5.0, $docente->fresh()->dias_utilizados);
        $this->assertEquals($docente->fresh()->dias_asignados - 5.0, $docente->fresh()->dias_disponibles);
    }

    public function test_notificacion_a_multiples_jefes_de_personal_llega_a_todos_al_mismo_tiempo(): void
    {
        $rolJefatura = Rol::where('codigo', Constantes::ROL_JEFATURA)->firstOrFail();

        // Asegurar que exista un segundo Jefe de Personal activo
        $jefe2 = Usuario::create([
            'ci' => '99887766',
            'codigo' => 'JEF002',
            'email' => 'segundo.jefe@unsxx.bo',
            'contrasena' => 'Unsxx2026',
            'rol_id' => $rolJefatura->id,
            'activo' => true,
            'debe_cambiar_contrasena' => false,
        ]);

        $jefe1 = Usuario::where('codigo', 'JEF001')->firstOrFail();

        // El docente DOC001 crea una solicitud que es aprobada por su director de carrera y pasa a Jefatura
        $docente = Usuario::where('codigo', 'DOC001')->firstOrFail()->docente;
        $solicitud = Solicitud::create([
            'docente_id' => $docente->id,
            'fecha_inicio' => now()->addDays(10)->toDateString(),
            'fecha_fin' => now()->addDays(11)->toDateString(),
            'jornada' => Constantes::JORNADA_COMPLETA,
            'dias_solicitados' => 2,
            'motivo' => 'Solicitud que debe alertar a todos los jefes de personal.',
            'estado' => Constantes::ESTADO_PENDIENTE,
        ]);

        $directorCarrera = Usuario::where('codigo', 'DIRC01')->firstOrFail();

        $this->actingAs($directorCarrera)->post('/direccion/solicitudes/'.$solicitud->id.'/decidir', [
            'decision' => 'aprobada',
        ])->assertRedirect('/direccion/pendientes');

        $this->assertSame(Constantes::ESTADO_PENDIENTE_JEFATURA, $solicitud->fresh()->estado);

        // Verificar que AMBOS jefes de personal recibieron la notificación al mismo tiempo
        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $jefe1->id,
            'solicitud_id' => $solicitud->id,
            'tipo' => 'pendiente_jefatura',
        ]);

        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $jefe2->id,
            'solicitud_id' => $solicitud->id,
            'tipo' => 'pendiente_jefatura',
        ]);
    }

    public function test_vista_de_boleta_e_impresion_incluyen_fecha_de_conclusion(): void
    {
        $jefatura = Usuario::where('codigo', 'JEF001')->firstOrFail();
        $docente = Usuario::where('codigo', 'DOC001')->firstOrFail()->docente;

        // 1. Ver la ficha del docente con la boleta de vacaciones
        $respuestaFicha = $this->actingAs($jefatura)->get('/jefatura/docentes/'.$docente->id);
        $respuestaFicha->assertOk();
        $respuestaFicha->assertSee('boleta-fecha-conclusion');
        $respuestaFicha->assertSee('F. Conclusión:');
        $respuestaFicha->assertSee('conclusión el');

        // 2. Crear una solicitud aprobada para que aparezca la tabla de historial
        Solicitud::create([
            'docente_id' => $docente->id,
            'fecha_inicio' => now()->subDays(5)->toDateString(),
            'fecha_fin' => now()->subDays(4)->toDateString(),
            'jornada' => Constantes::JORNADA_COMPLETA,
            'dias_solicitados' => 2,
            'motivo' => 'Licencia de prueba aprobada.',
            'estado' => Constantes::ESTADO_APROBADA,
        ]);

        $respuestaImpresion = $this->actingAs($jefatura)->get('/jefatura/imprimir/docente/'.$docente->id);
        $respuestaImpresion->assertOk();
        $respuestaImpresion->assertSee('Periodo (Inicio a Conclusión)');
    }
}
