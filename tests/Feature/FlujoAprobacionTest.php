<?php

namespace Tests\Feature;

use App\Models\Docente;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Soporte\Constantes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlujoAprobacionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_el_docente_avisa_a_ambos_directores_y_con_uno_pasa_a_jefatura(): void
    {
        $docenteUsuario = Usuario::where('codigo', 'DOC001')->first();
        $directorCarrera = Usuario::where('codigo', 'DIRC01')->first();
        $directorArea = Usuario::where('codigo', 'DIRA01')->first();

        $this->actingAs($docenteUsuario)->post('/solicitudes', [
            'fecha_inicio' => now()->addDays(20)->toDateString(),
            'fecha_fin' => now()->addDays(22)->toDateString(),
            'motivo' => 'Viaje académico institucional a un congreso.',
        ])->assertRedirect('/solicitudes');

        $solicitud = Solicitud::latest('id')->first();
        $this->assertSame(Constantes::ESTADO_PENDIENTE, $solicitud->estado);
        $this->assertEquals(3, $solicitud->dias_solicitados);
        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $directorCarrera->id,
            'tipo' => 'nueva_solicitud',
            'solicitud_id' => $solicitud->id,
        ]);
        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $directorArea->id,
            'tipo' => 'nueva_solicitud',
            'solicitud_id' => $solicitud->id,
        ]);

        $this->actingAs($directorCarrera)->post('/direccion/solicitudes/'.$solicitud->id.'/decidir', [
            'decision' => 'aprobada',
        ])->assertRedirect('/direccion/pendientes');

        $this->assertSame(Constantes::ESTADO_PENDIENTE_JEFATURA, $solicitud->fresh()->estado);

        $this->from('/direccion/pendientes')->actingAs($directorArea)->post('/direccion/solicitudes/'.$solicitud->id.'/decidir', [
            'decision' => 'aprobada',
        ])->assertRedirect('/direccion/pendientes')->assertSessionHasErrors();

        $jefatura = Usuario::where('codigo', 'JEF001')->first();
        $this->actingAs($jefatura)->post('/jefatura/solicitudes/'.$solicitud->id.'/decidir', [
            'decision' => 'aprobada',
        ])->assertRedirect('/jefatura/solicitudes/pendientes');

        $this->assertSame(Constantes::ESTADO_APROBADA, $solicitud->fresh()->estado);
        $this->assertEquals(3, Docente::find($solicitud->docente_id)->dias_utilizados);
    }

    public function test_el_director_de_area_tambien_puede_pasar_al_docente_a_jefatura(): void
    {
        $docenteUsuario = Usuario::where('codigo', 'DOC001')->first();
        $directorArea = Usuario::where('codigo', 'DIRA01')->first();

        $this->actingAs($docenteUsuario)->post('/solicitudes', [
            'fecha_inicio' => now()->addDays(24)->toDateString(),
            'fecha_fin' => now()->addDays(25)->toDateString(),
            'motivo' => 'Permiso del docente revisado solo por el director de área.',
        ])->assertRedirect('/solicitudes');

        $solicitud = Solicitud::latest('id')->first();

        $this->actingAs($directorArea)->post('/direccion/solicitudes/'.$solicitud->id.'/decidir', [
            'decision' => 'aprobada',
        ])->assertRedirect('/direccion/pendientes');

        $this->assertSame(Constantes::ESTADO_PENDIENTE_JEFATURA, $solicitud->fresh()->estado);
    }

    public function test_si_un_director_rechaza_al_docente_no_avanza(): void
    {
        $docenteUsuario = Usuario::where('codigo', 'DOC001')->first();
        $directorArea = Usuario::where('codigo', 'DIRA01')->first();

        $this->actingAs($docenteUsuario)->post('/solicitudes', [
            'fecha_inicio' => now()->addDays(26)->toDateString(),
            'fecha_fin' => now()->addDays(27)->toDateString(),
            'motivo' => 'Solicitud que será rechazada por dirección.',
        ])->assertRedirect('/solicitudes');

        $solicitud = Solicitud::latest('id')->first();

        $this->actingAs($directorArea)->post('/direccion/solicitudes/'.$solicitud->id.'/decidir', [
            'decision' => 'rechazada',
        ])->assertRedirect('/direccion/pendientes');

        $this->assertSame(Constantes::ESTADO_RECHAZADA, $solicitud->fresh()->estado);
        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $docenteUsuario->id,
            'tipo' => 'rechazada',
            'solicitud_id' => $solicitud->id,
        ]);
    }

    public function test_la_licencia_del_director_de_carrera_va_al_area_y_luego_a_jefatura(): void
    {
        $directorCarrera = Usuario::where('codigo', 'DIRC01')->first();
        $directorArea = Usuario::where('codigo', 'DIRA01')->first();

        $this->actingAs($directorCarrera)->post('/solicitudes', [
            'fecha_inicio' => now()->addDays(40)->toDateString(),
            'fecha_fin' => now()->addDays(41)->toDateString(),
            'motivo' => 'Permiso del director de carrera para un trámite institucional.',
        ])->assertRedirect('/solicitudes');

        $solicitud = Solicitud::latest('id')->first();
        $this->assertSame(Constantes::ESTADO_PENDIENTE_AREA, $solicitud->estado);
        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $directorArea->id,
            'tipo' => 'pendiente_area',
            'solicitud_id' => $solicitud->id,
        ]);

        $this->actingAs($directorArea)->post('/direccion/solicitudes/'.$solicitud->id.'/decidir', [
            'decision' => 'rechazada',
        ])->assertRedirect('/direccion/pendientes');

        $this->assertSame(Constantes::ESTADO_RECHAZADA, $solicitud->fresh()->estado);
        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $directorCarrera->id,
            'tipo' => 'rechazada',
            'solicitud_id' => $solicitud->id,
        ]);
    }

    public function test_el_director_de_area_va_directo_a_jefatura(): void
    {
        $directorArea = Usuario::where('codigo', 'DIRA01')->first();

        $this->actingAs($directorArea)->post('/solicitudes', [
            'fecha_inicio' => now()->addDays(30)->toDateString(),
            'fecha_fin' => now()->addDays(31)->toDateString(),
            'motivo' => 'Permiso personal del director de área.',
        ])->assertRedirect('/solicitudes');

        $solicitud = Solicitud::latest('id')->first();
        $this->assertSame(Constantes::ESTADO_PENDIENTE_JEFATURA, $solicitud->estado);
    }

    public function test_jefatura_no_puede_solicitar_licencia(): void
    {
        $jefatura = Usuario::where('codigo', 'JEF001')->first();
        $this->actingAs($jefatura)->get('/solicitudes/crear')->assertForbidden();
        $this->actingAs($jefatura)->get('/panel')->assertDontSee('Solicitar mi licencia');
    }

    public function test_el_consultor_no_puede_solicitar_licencia(): void
    {
        $consultor = Usuario::where('codigo', 'DOC004')->first();
        $this->actingAs($consultor)->get('/solicitudes/crear')->assertForbidden();
        $this->assertSame(0, $consultor->docente->dias_asignados);
    }

    public function test_el_docente_no_puede_aprobar(): void
    {
        $solicitud = Solicitud::where('estado', Constantes::ESTADO_PENDIENTE)->first();
        $docente = Usuario::where('codigo', 'DOC001')->first();

        $this->actingAs($docente)->post('/direccion/solicitudes/'.$solicitud->id.'/decidir', [
            'decision' => 'aprobada',
        ])->assertForbidden();
    }
}
