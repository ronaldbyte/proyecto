<?php

namespace Tests\Feature;

use App\Models\Docente;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Soporte\Constantes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LimpiarDatosPruebaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_jefatura_puede_limpiar_datos_de_prueba_y_dejar_solo_su_cuenta(): void
    {
        $jefatura = Usuario::where('codigo', 'JEF001')->firstOrFail();

        $this->assertGreaterThan(0, Docente::count());
        $this->assertGreaterThan(0, Solicitud::count());

        $respuesta = $this->actingAs($jefatura)->post('/jefatura/respaldo/limpiar');
        $respuesta->assertRedirect('/panel');
        $respuesta->assertSessionHas('exito');

        // Solo debe quedar el usuario de jefatura
        $this->assertSame(1, Usuario::count());
        $this->assertSame('JEF001', Usuario::first()->codigo);

        // Solo debe quedar el perfil docente de jefatura
        $this->assertSame(1, Docente::count());
        $this->assertSame($jefatura->id, Docente::first()->usuario_id);

        // Solicitudes vacías
        $this->assertSame(0, Solicitud::count());
    }

    public function test_un_docente_no_puede_ejecutar_la_limpieza(): void
    {
        $docente = Usuario::where('codigo', 'DOC001')->firstOrFail();

        $respuesta = $this->actingAs($docente)->post('/jefatura/respaldo/limpiar');
        $respuesta->assertForbidden();
    }
}

