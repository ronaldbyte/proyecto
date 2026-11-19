<?php

namespace Tests\Feature;

use App\Models\Carrera;
use App\Models\Docente;
use App\Models\Notificacion;
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

    public function test_notificacion_y_decision_final_exclusiva_de_jef001_titular(): void
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
            'motivo' => 'Solicitud que debe alertar exclusivamente al jefe titular JEF001.',
            'estado' => Constantes::ESTADO_PENDIENTE,
        ]);

        $directorCarrera = Usuario::where('codigo', 'DIRC01')->firstOrFail();

        $this->actingAs($directorCarrera)->post('/direccion/solicitudes/'.$solicitud->id.'/decidir', [
            'decision' => 'aprobada',
        ])->assertRedirect('/direccion/pendientes');

        $this->assertSame(Constantes::ESTADO_PENDIENTE_JEFATURA, $solicitud->fresh()->estado);

        // 1. Verificar que SOLO el Jefe Titular JEF001 recibió la notificación de decisión
        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $jefe1->id,
            'solicitud_id' => $solicitud->id,
            'tipo' => 'pendiente_jefatura',
        ]);

        // 2. El segundo jefe JEF002 NO debe recibir dicha notificación
        $this->assertDatabaseMissing('notificaciones', [
            'usuario_id' => $jefe2->id,
            'solicitud_id' => $solicitud->id,
            'tipo' => 'pendiente_jefatura',
        ]);

        // 3. El segundo jefe puede VER la solicitud y el botón para recordar al titular
        $respuestaVer = $this->actingAs($jefe2)->get('/solicitudes/'.$solicitud->id);
        $respuestaVer->assertOk();
        $respuestaVer->assertSee('Recordar notificación al Jefe');

        // 3.1. El segundo jefe puede enviar un recordatorio al titular JEF001
        $this->actingAs($jefe2)->post('/jefatura/solicitudes/'.$solicitud->id.'/recordar')
            ->assertSessionHas('exito');

        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $jefe1->id,
            'solicitud_id' => $solicitud->id,
            'tipo' => 'recordatorio_jefatura',
        ]);

        // 4. El segundo jefe NO puede emitir decisión (debe recibir 403)
        $this->actingAs($jefe2)->post('/jefatura/solicitudes/'.$solicitud->id.'/decidir', [
            'decision' => 'aprobada',
        ])->assertForbidden();

        // 5. El jefe titular JEF001 SÍ puede emitir decisión
        $this->actingAs($jefe1)->post('/jefatura/solicitudes/'.$solicitud->id.'/decidir', [
            'decision' => 'aprobada',
        ])->assertRedirect('/jefatura/solicitudes/pendientes');

        $this->assertSame(Constantes::ESTADO_APROBADA, $solicitud->fresh()->estado);
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
        $respuestaFicha->assertSee('Días pendientes');
        $respuestaFicha->assertSee('boleta-pendientes');
        $respuestaFicha->assertSee('Modalidad de Retorno:');
        $respuestaFicha->assertSee('Restaurar valores anteriores');
        $respuestaFicha->assertSee('boleta-hora-retorno');

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

    public function test_flujo_estricto_docente_notifica_a_director_carrera_y_area_simultaneamente_y_luego_a_jefatura(): void
    {
        $docenteUsuario = Usuario::where('codigo', 'DOC001')->firstOrFail();
        $dirCarrera = Usuario::where('codigo', 'DIRC01')->firstOrFail();
        $dirArea = Usuario::where('codigo', 'DIRA01')->firstOrFail();
        $jefe = Usuario::where('codigo', 'JEF001')->firstOrFail();

        // Limpiar notificaciones previas del seeder para una prueba limpia
        Notificacion::truncate();

        // 1. El docente envía la solicitud
        $this->actingAs($docenteUsuario)->post('/solicitudes', [
            'fecha_inicio' => now()->addDays(20)->toDateString(),
            'fecha_fin' => now()->addDays(22)->toDateString(),
            'jornada' => Constantes::JORNADA_COMPLETA,
            'motivo' => 'Solicitud para probar el flujo estricto docente -> directores -> jefatura.',
        ])->assertRedirect('/solicitudes');

        $solicitud = Solicitud::latest('id')->firstOrFail();

        // La solicitud debe iniciar en ESTADO_PENDIENTE
        $this->assertSame(Constantes::ESTADO_PENDIENTE, $solicitud->estado);

        // Debe llegar la notificación al Director de Carrera Y al Director de Área simultáneamente
        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $dirCarrera->id,
            'solicitud_id' => $solicitud->id,
            'tipo' => 'nueva_solicitud',
        ]);
        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $dirArea->id,
            'solicitud_id' => $solicitud->id,
            'tipo' => 'nueva_solicitud',
        ]);

        // Jefatura NO debe recibir notificación en este momento
        $this->assertDatabaseMissing('notificaciones', [
            'usuario_id' => $jefe->id,
            'solicitud_id' => $solicitud->id,
        ]);

        // Jefatura NO puede decidir la solicitud mientras esté en ESTADO_PENDIENTE
        $this->actingAs($jefe)->post('/jefatura/solicitudes/'.$solicitud->id.'/decidir', [
            'decision' => 'aprobada',
        ])->assertSessionHasErrors('general');

        $this->assertSame(Constantes::ESTADO_PENDIENTE, $solicitud->fresh()->estado);

        // 2. Uno de los dos directores (ej. Director de Carrera) aprueba la solicitud
        $this->actingAs($dirCarrera)->post('/direccion/solicitudes/'.$solicitud->id.'/decidir', [
            'decision' => 'aprobada',
        ])->assertRedirect('/direccion/pendientes');

        // El estado ahora pasa a PENDIENTE_JEFATURA
        $this->assertSame(Constantes::ESTADO_PENDIENTE_JEFATURA, $solicitud->fresh()->estado);

        // RECIÉN ahora la Jefatura recibe la notificación de aprobación pendiente
        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $jefe->id,
            'solicitud_id' => $solicitud->id,
            'tipo' => 'pendiente_jefatura',
        ]);

        // 3. Jefatura de Personal toma la decisión final
        $this->actingAs($jefe)->post('/jefatura/solicitudes/'.$solicitud->id.'/decidir', [
            'decision' => 'aprobada',
        ])->assertRedirect('/jefatura/solicitudes/pendientes');

        $solicitudActualizada = $solicitud->fresh();
        $this->assertSame(Constantes::ESTADO_APROBADA, $solicitudActualizada->estado);

        // Se descuentan los 3 días de vacaciones en tiempo real
        $this->assertEquals(3.0, $docenteUsuario->docente->fresh()->dias_utilizados);

        // El docente recibe la notificación de que su solicitud fue aprobada
        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $docenteUsuario->id,
            'solicitud_id' => $solicitud->id,
            'tipo' => 'aprobada',
        ]);
    }

    public function test_registro_de_usuario_genera_codigo_segun_rol_seleccionado(): void
    {
        $jefe = Usuario::where('codigo', 'JEF001')->firstOrFail();
        $rolDirectorCarrera = Rol::where('codigo', Constantes::ROL_DIRECTOR_CARRERA)->firstOrFail();
        $rolDirectorArea = Rol::where('codigo', Constantes::ROL_DIRECTOR_AREA)->firstOrFail();
        $rolDocente = Rol::where('codigo', Constantes::ROL_DOCENTE)->firstOrFail();

        // 1. Probar que sugerir-codigo sin rol devuelve vacío
        $this->actingAs($jefe)->getJson('/jefatura/docentes/codigo')
            ->assertOk()
            ->assertJson(['codigo' => '']);

        // 2. Probar que sugerir-codigo con Director de Carrera devuelve prefijo DIRC
        $respuestaDirC = $this->actingAs($jefe)->getJson('/jefatura/docentes/codigo?rol_id='.$rolDirectorCarrera->id)
            ->assertOk();
        $this->assertStringStartsWith('DIRC', $respuestaDirC->json('codigo'));

        // 3. Probar que sugerir-codigo con Director de Área devuelve prefijo DIRA
        $respuestaDirA = $this->actingAs($jefe)->getJson('/jefatura/docentes/codigo?rol_id='.$rolDirectorArea->id)
            ->assertOk();
        $this->assertStringStartsWith('DIRA', $respuestaDirA->json('codigo'));

        // 4. Probar que sugerir-codigo con Docente devuelve prefijo DOC
        $respuestaDoc = $this->actingAs($jefe)->getJson('/jefatura/docentes/codigo?rol_id='.$rolDocente->id)
            ->assertOk();
        $this->assertStringStartsWith('DOC', $respuestaDoc->json('codigo'));
    }

    public function test_seguridad_ci_email_y_codigo_no_se_pueden_duplicar(): void
    {
        $jefe = Usuario::where('codigo', 'JEF001')->firstOrFail();
        $docenteExistente = Usuario::where('codigo', 'DOC001')->firstOrFail();
        $carrera = Carrera::firstOrFail();
        $rolDocente = Rol::where('codigo', Constantes::ROL_DOCENTE)->firstOrFail();

        // 1. Intentar registrar un docente con el mismo CI que otro usuario existente
        $respuestaCiDuplicado = $this->actingAs($jefe)->post('/jefatura/docentes', [
            'ci' => $docenteExistente->ci,
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
            'email' => 'juan.perez.nuevo@unsxx.bo',
            'cargo' => 'Docente',
            'tipo_contrato' => 'titular',
            'carrera_id' => $carrera->id,
            'rol_id' => $rolDocente->id,
            'fecha_ingreso' => '2020-01-01',
        ]);
        $respuestaCiDuplicado->assertSessionHasErrors(['ci']);

        // 2. Intentar registrar un docente con el mismo Correo electrónico que otro usuario existente
        $respuestaEmailDuplicado = $this->actingAs($jefe)->post('/jefatura/docentes', [
            'ci' => '123456789',
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
            'email' => $docenteExistente->email,
            'cargo' => 'Docente',
            'tipo_contrato' => 'titular',
            'carrera_id' => $carrera->id,
            'rol_id' => $rolDocente->id,
            'fecha_ingreso' => '2020-01-01',
        ]);
        $respuestaEmailDuplicado->assertSessionHasErrors(['email']);

        // 3. Probar que los códigos generados son únicos en la base de datos
        $codigos = [];
        for ($i = 0; $i < 20; $i++) {
            $nuevoCodigo = Usuario::generarCodigoUnico(Constantes::ROL_DOCENTE);
            $this->assertFalse(in_array($nuevoCodigo, $codigos, true));
            $this->assertFalse(Usuario::where('codigo', $nuevoCodigo)->exists());
            $codigos[] = $nuevoCodigo;
        }
    }
}
