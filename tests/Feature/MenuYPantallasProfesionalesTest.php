<?php

namespace Tests\Feature;

use App\Models\Aprobacion;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Soporte\Constantes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuYPantallasProfesionalesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_el_menu_de_jefatura_tiene_un_solo_enlace_de_solicitudes_y_configuracion(): void
    {
        $jefatura = Usuario::where('codigo', 'JEF001')->firstOrFail();

        $this->actingAs($jefatura)->get('/panel')
            ->assertOk()
            ->assertSee('Solicitudes')
            ->assertSee('Configuración')
            ->assertSee('Copia de seguridad')
            ->assertDontSee('Decisión final')
            ->assertDontSee('Todas las solicitudes')
            ->assertDontSee('Imprimir solicitudes')
            ->assertDontSee('Avisos al celular')
            ->assertDontSee('Cambiar contraseña');
    }

    public function test_la_configuracion_de_jefatura_muestra_las_tres_pestanas(): void
    {
        $jefatura = Usuario::where('codigo', 'JEF001')->firstOrFail();

        foreach (['/perfil', '/contrasena', '/jefatura/avisos'] as $ruta) {
            $this->actingAs($jefatura)->get($ruta)
                ->assertOk()
                ->assertSee('Mi perfil')
                ->assertSee('Cambiar contraseña')
                ->assertSee('Avisos al celular');
        }
    }

    public function test_la_configuracion_del_docente_no_muestra_avisos_ni_respaldo(): void
    {
        $docente = Usuario::where('codigo', 'DOC001')->firstOrFail();

        $this->actingAs($docente)->get('/perfil')
            ->assertOk()
            ->assertSee('Configuración')
            ->assertSee('Cambiar contraseña')
            ->assertDontSee('Avisos al celular')
            ->assertDontSee('Copia de seguridad');
    }

    public function test_las_solicitudes_de_jefatura_se_organizan_en_pendientes_e_historial(): void
    {
        $jefatura = Usuario::where('codigo', 'JEF001')->firstOrFail();

        foreach (['/jefatura/solicitudes/pendientes', '/jefatura/solicitudes'] as $ruta) {
            $this->actingAs($jefatura)->get($ruta)
                ->assertOk()
                ->assertSee('Pendientes')
                ->assertSee('Historial');
        }
    }

    public function test_una_solicitud_de_medio_dia_se_muestra_como_medio_dia_y_no_como_decimal(): void
    {
        $solicitud = new Solicitud([
            'jornada' => Constantes::JORNADA_MANANA,
            'dias_solicitados' => 0.5,
        ]);

        $this->assertSame('Medio día (mañana)', $solicitud->textoDias());

        $solicitud->jornada = Constantes::JORNADA_TARDE;
        $this->assertSame('Medio día (tarde)', $solicitud->textoDias());

        $solicitud->jornada = Constantes::JORNADA_COMPLETA;
        $solicitud->dias_solicitados = 3;
        $this->assertSame('3', $solicitud->textoDias());
    }

    public function test_el_formulario_de_solicitud_se_envia_sin_confirmacion_previa(): void
    {
        $docente = Usuario::where('codigo', 'DOC001')->firstOrFail();

        $this->actingAs($docente)->get('/solicitudes/crear')
            ->assertOk()
            ->assertDontSee('data-confirmar', false);
    }

    public function test_los_formularios_de_decision_se_procesan_directamente_sin_dialogos_de_confirmacion(): void
    {
        $jefatura = Usuario::where('codigo', 'JEF001')->firstOrFail();
        $solicitud = Solicitud::where('estado', Constantes::ESTADO_PENDIENTE_JEFATURA)->first();

        if (! $solicitud) {
            $docente = Usuario::where('codigo', 'DOC001')->firstOrFail()->docente;
            $solicitud = Solicitud::create([
                'docente_id' => $docente->id,
                'fecha_inicio' => now()->addDays(5)->toDateString(),
                'fecha_fin' => now()->addDays(6)->toDateString(),
                'jornada' => Constantes::JORNADA_COMPLETA,
                'dias_solicitados' => 2,
                'motivo' => 'Motivo de prueba',
                'estado' => Constantes::ESTADO_PENDIENTE_JEFATURA,
            ]);
        }

        $this->actingAs($jefatura)->get('/solicitudes/'.$solicitud->id)
            ->assertOk()
            ->assertDontSee('data-confirmar', false);
    }

    public function test_el_docente_puede_imprimir_su_historial_oficial_de_licencias(): void
    {
        $docenteUsuario = Usuario::where('codigo', 'DOC001')->firstOrFail();
        $docente = $docenteUsuario->docente;

        // Crear una solicitud aprobada con aprobación
        $solicitud = Solicitud::create([
            'docente_id' => $docente->id,
            'fecha_inicio' => now()->subDays(10)->toDateString(),
            'fecha_fin' => now()->subDays(8)->toDateString(),
            'jornada' => Constantes::JORNADA_COMPLETA,
            'dias_solicitados' => 3,
            'motivo' => 'Licencia académica aprobada',
            'estado' => Constantes::ESTADO_APROBADA,
        ]);

        $jefatura = Usuario::where('codigo', 'JEF001')->firstOrFail();
        Aprobacion::create([
            'solicitud_id' => $solicitud->id,
            'usuario_id' => $jefatura->id,
            'rol_codigo' => Constantes::ROL_JEFATURA,
            'etapa' => 'jefatura',
            'decision' => 'aprobada',
            'observacion' => 'Aprobado conforme a norma',
            'estado_anterior' => Constantes::ESTADO_PENDIENTE_JEFATURA,
            'estado_nuevo' => Constantes::ESTADO_APROBADA,
        ]);

        $this->actingAs($docenteUsuario)->get('/historial/imprimir')
            ->assertOk()
            ->assertSee('HISTORIAL DE LICENCIAS')
            ->assertSee('Pedro Condori López')
            ->assertSee('Licencia académica aprobada')
            ->assertSee('Jefatura de Personal')
            ->assertSee('Decidido por')
            ->assertSee('Fecha y Hora')
            ->assertDontSee('Firma del Docente')
            ->assertDontSee('Firma y Sello')
            ->assertDontSee('C.I. / Código')
            ->assertDontSee('Tipo Contrato');
    }
}
