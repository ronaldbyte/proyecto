<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccesoYFlujoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_la_portada_redirige_al_ingreso(): void
    {
        $this->get('/')->assertRedirect('/ingresar');
    }

    public function test_la_pantalla_de_ingreso_se_muestra(): void
    {
        $this->get('/ingresar')
            ->assertOk()
            ->assertSee('Gestión de Licencias')
            ->assertSee('Universidad Nacional');
    }

    public function test_un_docente_ingresa_y_ve_su_panel(): void
    {
        $respuesta = $this->post('/ingresar', [
            'codigo' => 'DOC001',
            'contrasena' => 'Unsxx2026',
        ]);

        $respuesta->assertRedirect('/panel');
        $this->get('/panel')
            ->assertOk()
            ->assertSee('Pedro Condori López')
            ->assertSee('Solicitar licencia')
            ->assertDontSee('← Volver');
    }

    public function test_un_docente_no_entra_a_jefatura(): void
    {
        $this->post('/ingresar', [
            'codigo' => 'DOC001',
            'contrasena' => 'Unsxx2026',
        ]);

        $this->get('/jefatura/docentes')->assertForbidden();
    }

    public function test_el_director_ve_solicitudes_pendientes(): void
    {
        $this->post('/ingresar', [
            'codigo' => 'DIRC01',
            'contrasena' => 'Unsxx2026',
        ]);

        $this->get('/panel')->assertOk()->assertSee('Panel de dirección');
        $this->get('/direccion/pendientes')->assertOk()->assertSee('← Volver');
    }

    public function test_jefatura_ve_el_panel_administrativo(): void
    {
        $this->post('/ingresar', [
            'codigo' => 'JEF001',
            'contrasena' => 'Unsxx2026',
        ]);

        $this->get('/panel')
            ->assertOk()
            ->assertSee('Jefatura de Personal')
            ->assertSee('Docentes con días excedidos')
            ->assertDontSee('Solicitar mi licencia')
            ->assertDontSee('MI LICENCIA');

        $this->get('/jefatura/docentes/crear')
            ->assertOk()
            ->assertSee('Vigencia del contrato')
            ->assertSee('← Volver')
            ->assertSee('Área Salud')
            ->assertSee('Bioquímica Farmacia')
            ->assertSee('Agronomía')
            ->assertSee('Contaduría Pública')
            ->assertDontSee('name="dias_utilizados"', false)
            ->assertSee('Código de ingreso')
            ->assertSee('name="codigo"', false);

        $this->get('/notificaciones')
            ->assertOk()
            ->assertSee('Notificaciones');

        $this->get('/notificaciones/estado')
            ->assertOk()
            ->assertJsonStructure(['cantidad']);

        $this->get('/jefatura/avisos')
            ->assertOk()
            ->assertSee('Gmail del sistema')
            ->assertSee('Activar avisos al celular')
            ->assertDontSee('Gmail de Jefatura');

        $this->get('/jefatura/personal')
            ->assertOk()
            ->assertSee('María Elena')
            ->assertSee('jefatura@unsxx.bo');

        $this->get('/jefatura/personal/crear')->assertOk()->assertSee('Registrar jefe de personal');
    }

    public function test_codigo_incorrecto_no_ingresa(): void
    {
        $this->from('/ingresar')->post('/ingresar', [
            'codigo' => 'XXX',
            'contrasena' => 'mala',
        ])->assertRedirect('/ingresar');
    }

    public function test_un_docente_puede_consultar_su_codigo_por_ci(): void
    {
        $usuario = \App\Models\Usuario::where('codigo', 'DOC001')->firstOrFail();
        $usuario->update(['ci' => '1234567']);

        $respuesta = $this->postJson('/consultar-codigo', ['ci' => '1234567']);
        $respuesta->assertOk()
            ->assertJson([
                'exito' => true,
            ])
            ->assertJsonFragment(['codigo' => 'DOC001']);
    }

    public function test_consulta_codigo_con_ci_inexistente_retorna_404(): void
    {
        $respuesta = $this->postJson('/consultar-codigo', ['ci' => '99999999']);
        $respuesta->assertNotFound()
            ->assertJson(['exito' => false]);
    }
}
