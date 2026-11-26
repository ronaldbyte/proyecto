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

class SimulacionCompletaSolicitudesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Solicitud::query()->delete();
        Docente::query()->update(['dias_utilizados' => 0]);
    }

    private function crearPersona(string $codigo, string $rolCodigo, string $carreraCodigo, string $contrato = Constantes::CONTRATO_TITULAR): Usuario
    {
        $carrera = Carrera::where('codigo', $carreraCodigo)->firstOrFail();

        $usuario = Usuario::create([
            'ci' => (string) random_int(1000000, 9999999),
            'codigo' => $codigo,
            'email' => strtolower($codigo).'@unsxx.bo',
            'contrasena' => 'Unsxx2026',
            'rol_id' => Rol::where('codigo', $rolCodigo)->firstOrFail()->id,
            'activo' => true,
            'debe_cambiar_contrasena' => false,
        ]);

        Docente::create([
            'usuario_id' => $usuario->id,
            'nombres' => 'Persona',
            'apellidos' => $codigo,
            'telefono' => '70000000',
            'cargo' => 'Docente',
            'carrera_id' => $carrera->id,
            'area_id' => $carrera->area_id,
            'fecha_ingreso' => '2015-01-01',
            'tipo_contrato' => $contrato,
            'dias_utilizados' => 0,
        ]);

        return $usuario->fresh();
    }

    private function pedirLicencia(Usuario $usuario, int $diasDesdeHoy, string $jornada = Constantes::JORNADA_COMPLETA, int $duracion = 1): Solicitud
    {
        $datos = [
            'fecha_inicio' => now()->addDays($diasDesdeHoy)->toDateString(),
            'fecha_fin' => now()->addDays($diasDesdeHoy + $duracion - 1)->toDateString(),
            'jornada' => $jornada,
            'motivo' => 'Motivo de simulación para la solicitud de licencia.',
        ];

        $this->actingAs($usuario)->post('/solicitudes', $datos)->assertRedirect('/solicitudes');

        return Solicitud::latest('id')->firstOrFail();
    }

    private function aprobarCadenaCompleta(Solicitud $solicitud, Usuario $directorCarrera, Usuario $jefatura): void
    {
        $this->actingAs($directorCarrera)
            ->post('/direccion/solicitudes/'.$solicitud->id.'/decidir', ['decision' => 'aprobada'])
            ->assertRedirect('/direccion/pendientes');

        $this->actingAs($jefatura)
            ->post('/jefatura/solicitudes/'.$solicitud->id.'/decidir', ['decision' => 'aprobada'])
            ->assertRedirect('/jefatura/solicitudes/pendientes');
    }

    public function test_dia_entero_mas_medio_dia_suma_uno_y_medio_en_panel_ficha_y_boleta(): void
    {
        $pedro = Usuario::where('codigo', 'DOC001')->firstOrFail();
        $directorCarrera = Usuario::where('codigo', 'DIRC01')->firstOrFail();
        $jefatura = Usuario::where('codigo', 'JEF001')->firstOrFail();
        $docente = $pedro->docente;

        $entero = $this->pedirLicencia($pedro, 30);
        $this->aprobarCadenaCompleta($entero, $directorCarrera, $jefatura);
        $this->assertEquals(1.0, $docente->fresh()->dias_utilizados);

        $medio = $this->pedirLicencia($pedro, 31, Constantes::JORNADA_TARDE);

        $this->assertEquals(1.0, $docente->fresh()->dias_utilizados);
        $this->assertEquals(0.5, $docente->fresh()->dias_pendientes);
        $this->actingAs($pedro)->get('/panel')
            ->assertOk()
            ->assertSee('id="lbl-dias-licencias">1</strong>', false)
            ->assertSee('Medio día (tarde)');

        $this->aprobarCadenaCompleta($medio, $directorCarrera, $jefatura);

        $docente = $docente->fresh();
        $this->assertEquals(1.5, $docente->dias_utilizados);
        $this->assertEquals(0.0, $docente->dias_pendientes);
        $this->assertEquals($docente->dias_asignados - 1.5, $docente->dias_disponibles);
        $this->assertSame('1 día y medio', $docente->textoDias($docente->dias_utilizados));

        $this->actingAs($pedro)->get('/panel')
            ->assertOk()
            ->assertSee('id="lbl-dias-licencias">1.5</strong>', false)
            ->assertSee('id="lbl-dias-disponibles">'.(float) ($docente->dias_asignados - 1.5).'</strong>', false);

        $this->actingAs($jefatura)->get(route('jefatura.docentes.ver', $docente))
            ->assertOk()
            ->assertSee('id="lbl-dias-licencias">1.5</strong>', false)
            ->assertSee('const diasUtilizadosOriginal = 1.5;', false)
            ->assertSee('boleta-total-texto', false)
            ->assertSee('step="0.5" id="inp-licencias-pasadas"', false)
            ->assertSee('step="0.5" id="inp-pendientes"', false);

        $this->actingAs($jefatura)->get(route('jefatura.imprimir.docente', $docente))
            ->assertOk()
            ->assertSee('Medio día (tarde)')
            ->assertSee('Aprobada');
    }

    public function test_un_director_no_puede_decidir_solicitudes_de_otra_carrera_ni_de_otra_area(): void
    {
        $rosa = Usuario::where('codigo', 'DOC003')->firstOrFail();
        $dirCarreraInf = Usuario::where('codigo', 'DIRC01')->firstOrFail();
        $dirAreaTec = Usuario::where('codigo', 'DIRA01')->firstOrFail();
        $dirCarreraEnf = $this->crearPersona('DIRCENF', Constantes::ROL_DIRECTOR_CARRERA, 'ENF');
        $dirAreaSal = $this->crearPersona('DIRASAL', Constantes::ROL_DIRECTOR_AREA, 'ENF');

        $solicitud = $this->pedirLicencia($rosa, 20, Constantes::JORNADA_COMPLETA, 2);
        $this->assertSame(Constantes::ESTADO_PENDIENTE, $solicitud->estado);

        foreach ([$dirCarreraEnf, $dirAreaSal] as $correcto) {
            $this->assertDatabaseHas('notificaciones', [
                'usuario_id' => $correcto->id,
                'solicitud_id' => $solicitud->id,
            ]);
        }
        foreach ([$dirCarreraInf, $dirAreaTec] as $ajeno) {
            $this->assertSame(0, Notificacion::where('usuario_id', $ajeno->id)->where('solicitud_id', $solicitud->id)->count());
            $this->actingAs($ajeno)->get(route('solicitudes.ver', $solicitud))->assertForbidden();
            $this->actingAs($ajeno)->post('/direccion/solicitudes/'.$solicitud->id.'/decidir', ['decision' => 'aprobada']);
            $this->assertSame(Constantes::ESTADO_PENDIENTE, $solicitud->fresh()->estado);
        }

        $this->actingAs($dirCarreraEnf)->post('/direccion/solicitudes/'.$solicitud->id.'/decidir', ['decision' => 'aprobada'])
            ->assertRedirect('/direccion/pendientes');
        $this->assertSame(Constantes::ESTADO_PENDIENTE_JEFATURA, $solicitud->fresh()->estado);

        $jefatura = Usuario::where('codigo', 'JEF001')->firstOrFail();
        $this->actingAs($jefatura)->post('/jefatura/solicitudes/'.$solicitud->id.'/decidir', ['decision' => 'aprobada']);
        $this->assertEquals(2.0, $rosa->docente->fresh()->dias_utilizados);
    }

    public function test_cada_area_y_carrera_recorre_el_flujo_completo(): void
    {
        $jefatura = Usuario::where('codigo', 'JEF001')->firstOrFail();
        $combinaciones = [
            ['carrera' => 'INF', 'area' => 'TEC'],
            ['carrera' => 'ENF', 'area' => 'SAL'],
            ['carrera' => 'DER', 'area' => 'SOC'],
        ];

        foreach ($combinaciones as $indice => $combinacion) {
            $c = $combinacion['carrera'];
            $docente = $this->crearPersona("DOC{$c}X", Constantes::ROL_DOCENTE, $c);
            $dirCarrera = $this->crearPersona("DIRC{$c}X", Constantes::ROL_DIRECTOR_CARRERA, $c);
            $this->crearPersona("DIRA{$c}X", Constantes::ROL_DIRECTOR_AREA, $c);

            $solicitud = $this->pedirLicencia($docente, 50 + $indice * 5, Constantes::JORNADA_MANANA);
            $this->aprobarCadenaCompleta($solicitud, $dirCarrera, $jefatura);

            $this->assertEquals(0.5, $docente->docente->fresh()->dias_utilizados, "Falló en carrera {$c}");
            $this->actingAs($docente)->get('/panel')->assertOk()->assertSee('id="lbl-dias-licencias">0.5</strong>', false);
        }

        $this->actingAs($jefatura)->get(route('jefatura.solicitudes.listar', ['area_id' => Carrera::where('codigo', 'ENF')->first()->area_id]))
            ->assertOk()
            ->assertSee('Medio día (mañana)');
    }

    public function test_el_director_de_carrera_pasa_por_el_area_y_su_medio_dia_se_acumula(): void
    {
        $dirCarrera = $this->crearPersona('DIRCDER', Constantes::ROL_DIRECTOR_CARRERA, 'DER');
        $dirArea = $this->crearPersona('DIRASOC', Constantes::ROL_DIRECTOR_AREA, 'DER');
        $jefatura = Usuario::where('codigo', 'JEF001')->firstOrFail();

        $entero = $this->pedirLicencia($dirCarrera, 10);
        $this->assertSame(Constantes::ESTADO_PENDIENTE_AREA, $entero->estado);
        $medio = $this->pedirLicencia($dirCarrera, 11, Constantes::JORNADA_MANANA);
        $this->assertSame(Constantes::ESTADO_PENDIENTE_AREA, $medio->estado);

        foreach ([$entero, $medio] as $solicitud) {
            $this->actingAs($dirArea)->post('/direccion/solicitudes/'.$solicitud->id.'/decidir', ['decision' => 'aprobada'])
                ->assertRedirect('/direccion/pendientes');
            $this->actingAs($jefatura)->post('/jefatura/solicitudes/'.$solicitud->id.'/decidir', ['decision' => 'aprobada']);
        }

        $this->assertEquals(1.5, $dirCarrera->docente->fresh()->dias_utilizados);
        $this->actingAs($dirCarrera)->get('/panel')->assertOk();
    }

    public function test_los_medios_dias_no_se_traslapan_pero_manana_y_tarde_conviven(): void
    {
        $pedro = Usuario::where('codigo', 'DOC001')->firstOrFail();

        $this->pedirLicencia($pedro, 15, Constantes::JORNADA_MANANA);
        $this->pedirLicencia($pedro, 15, Constantes::JORNADA_TARDE);
        $this->assertSame(2, Solicitud::count());

        $fecha = now()->addDays(15)->toDateString();
        $this->actingAs($pedro)->post('/solicitudes', [
            'fecha_inicio' => $fecha,
            'jornada' => Constantes::JORNADA_MANANA,
            'motivo' => 'Intento duplicado de la misma mañana.',
        ])->assertSessionHasErrors('fecha_inicio');

        $this->actingAs($pedro)->post('/solicitudes', [
            'fecha_inicio' => $fecha,
            'fecha_fin' => $fecha,
            'jornada' => Constantes::JORNADA_COMPLETA,
            'motivo' => 'Intento de día completo sobre medios días.',
        ])->assertSessionHasErrors('fecha_inicio');

        $this->assertSame(2, Solicitud::count());
    }

    public function test_solicitudes_rechazadas_o_canceladas_no_descuentan_dias(): void
    {
        $pedro = Usuario::where('codigo', 'DOC001')->firstOrFail();
        $jefatura = Usuario::where('codigo', 'JEF001')->firstOrFail();
        $directorCarrera = Usuario::where('codigo', 'DIRC01')->firstOrFail();
        $docente = $pedro->docente;

        $aCancelar = $this->pedirLicencia($pedro, 12, Constantes::JORNADA_MANANA);
        $this->assertEquals(0.5, $docente->fresh()->dias_pendientes);
        $this->actingAs($pedro)->post(route('solicitudes.cancelar', $aCancelar), ['motivo_cancelacion' => 'Ya no lo necesito.'])
            ->assertRedirect('/solicitudes');
        $this->assertSame(Constantes::ESTADO_CANCELADA, $aCancelar->fresh()->estado);
        $this->assertEquals(0.0, $docente->fresh()->dias_pendientes);

        $aRechazar = $this->pedirLicencia($pedro, 12, Constantes::JORNADA_MANANA);
        $this->actingAs($directorCarrera)->post('/direccion/solicitudes/'.$aRechazar->id.'/decidir', ['decision' => 'aprobada']);
        $this->actingAs($jefatura)->post('/jefatura/solicitudes/'.$aRechazar->id.'/decidir', ['decision' => 'rechazada', 'observacion' => 'Sin cupo.']);

        $this->assertSame(Constantes::ESTADO_RECHAZADA, $aRechazar->fresh()->estado);
        $this->assertEquals(0.0, $docente->fresh()->dias_utilizados);
    }

    public function test_un_docente_con_saldo_excedido_ve_sus_dias_excedidos_en_el_recuadro(): void
    {
        $lucia = Usuario::where('codigo', 'DOC002')->firstOrFail();
        $lucia->docente->forceFill(['dias_utilizados' => 16.5])->saveQuietly();

        $docente = $lucia->docente->fresh();
        $this->assertEquals($docente->dias_asignados, 15);
        $this->assertEquals(1.5, $docente->dias_excedidos);
        $this->assertEquals(0.0, $docente->dias_disponibles);

        $this->actingAs($lucia)->get('/panel')
            ->assertOk()
            ->assertSee('id="lbl-dias-excedidos">1.5</strong>', false);
    }

    public function test_todas_las_pantallas_cargan_sin_error_para_cada_rol(): void
    {
        $docenteRuta = Docente::whereHas('usuario', fn ($q) => $q->where('codigo', 'DOC001'))->firstOrFail();
        $solicitud = $this->pedirLicencia(Usuario::where('codigo', 'DOC001')->firstOrFail(), 8, Constantes::JORNADA_TARDE);
        $carreraEnf = Carrera::where('codigo', 'ENF')->firstOrFail();

        $rutasComunes = ['/panel', '/solicitudes', '/solicitudes/crear', '/historial', '/notificaciones', '/perfil', '/contrasena'];
        foreach (['DOC001', 'DOC003', 'DIRC01', 'DIRA01'] as $codigo) {
            $usuario = Usuario::where('codigo', $codigo)->firstOrFail();
            foreach ($rutasComunes as $ruta) {
                $this->actingAs($usuario)->get($ruta)->assertOk();
            }
        }

        foreach (['DIRC01', 'DIRA01'] as $codigo) {
            $usuario = Usuario::where('codigo', $codigo)->firstOrFail();
            $this->actingAs($usuario)->get('/direccion/pendientes')->assertOk();
            $this->actingAs($usuario)->get('/direccion/historial')->assertOk();
        }

        $jefatura = Usuario::where('codigo', 'JEF001')->firstOrFail();
        $rutasJefatura = [
            '/panel',
            '/perfil',
            '/jefatura/docentes',
            '/jefatura/docentes?area_id='.$carreraEnf->area_id.'&carrera_id='.$carreraEnf->id,
            '/jefatura/docentes/crear',
            route('jefatura.docentes.ver', $docenteRuta),
            route('jefatura.docentes.editar', $docenteRuta),
            '/jefatura/solicitudes',
            '/jefatura/solicitudes?estado=pendiente&area_id='.$carreraEnf->area_id,
            '/jefatura/solicitudes/pendientes',
            route('solicitudes.ver', $solicitud),
            route('jefatura.historial.docente', $docenteRuta),
            '/jefatura/roles',
            '/jefatura/personal',
            '/jefatura/personal/crear',
            '/jefatura/avisos',
            '/jefatura/respaldo',
            route('jefatura.imprimir.docente', $docenteRuta),
            '/jefatura/imprimir/solicitudes',
        ];
        foreach ($rutasJefatura as $ruta) {
            $this->actingAs($jefatura)->get($ruta)->assertOk();
        }

        $this->actingAs(Usuario::where('codigo', 'DOC001')->firstOrFail())->get('/jefatura/docentes')->assertForbidden();
        $this->actingAs(Usuario::where('codigo', 'DIRC01')->firstOrFail())->get('/jefatura/solicitudes')->assertForbidden();
    }
}
