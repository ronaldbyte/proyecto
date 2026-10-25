<?php

namespace Database\Seeders;

use App\Models\Carrera;
use App\Models\Docente;
use App\Models\Rol;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Soporte\Constantes;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre' => 'Docente', 'codigo' => Constantes::ROL_DOCENTE, 'descripcion' => 'Solicita licencias y vacaciones.'],
            ['nombre' => 'Director de Carrera', 'codigo' => Constantes::ROL_DIRECTOR_CARRERA, 'descripcion' => 'Revisa solicitudes de su carrera.'],
            ['nombre' => 'Director de Área', 'codigo' => Constantes::ROL_DIRECTOR_AREA, 'descripcion' => 'Revisa solicitudes de su área.'],
            ['nombre' => 'Jefatura de Personal', 'codigo' => Constantes::ROL_JEFATURA, 'descripcion' => 'Administración y decisión final.'],
        ];

        foreach ($roles as $rol) {
            Rol::create($rol);
        }

        $this->call(CatalogoAcademicoSeeder::class);

        $informatica = Carrera::where('codigo', 'INF')->firstOrFail();
        $enfermeria = Carrera::where('codigo', 'ENF')->firstOrFail();

        $this->crearPersona([
            'codigo' => 'JEF001',
            'email' => 'jefatura@unsxx.bo',
            'rol' => Constantes::ROL_JEFATURA,
            'nombres' => 'María Elena',
            'apellidos' => 'Quispe Mamani',
            'cargo' => 'Jefa de Personal',
            'carrera' => $informatica,
            'telefono' => '71234567',
            'fecha_ingreso' => '2012-03-01',
            'tipo_contrato' => Constantes::CONTRATO_TITULAR,
            'dias_utilizados' => 0,
        ]);

        $this->crearPersona([
            'codigo' => 'DIRA01',
            'email' => 'dir.tecnologia@unsxx.bo',
            'rol' => Constantes::ROL_DIRECTOR_AREA,
            'nombres' => 'Carlos Alberto',
            'apellidos' => 'Mamani Flores',
            'cargo' => 'Director de Área Tecnología',
            'carrera' => $informatica,
            'telefono' => '71876543',
            'fecha_ingreso' => '2010-02-15',
            'tipo_contrato' => Constantes::CONTRATO_TITULAR,
            'dias_utilizados' => 8,
        ]);

        $this->crearPersona([
            'codigo' => 'DIRC01',
            'email' => 'dir.informatica@unsxx.bo',
            'rol' => Constantes::ROL_DIRECTOR_CARRERA,
            'nombres' => 'Ana Lucía',
            'apellidos' => 'Flores Condori',
            'cargo' => 'Directora de Carrera de Informática',
            'carrera' => $informatica,
            'telefono' => '71999888',
            'fecha_ingreso' => '2014-08-20',
            'tipo_contrato' => Constantes::CONTRATO_TITULAR,
            'dias_utilizados' => 6,
        ]);

        $pedro = $this->crearPersona([
            'codigo' => 'DOC001',
            'email' => 'pedro.condori@unsxx.bo',
            'rol' => Constantes::ROL_DOCENTE,
            'nombres' => 'Pedro',
            'apellidos' => 'Condori López',
            'cargo' => 'Docente de tiempo completo',
            'carrera' => $informatica,
            'telefono' => '71112233',
            'fecha_ingreso' => '2018-01-10',
            'tipo_contrato' => Constantes::CONTRATO_TITULAR,
            'dias_utilizados' => 5,
        ]);

        $this->crearPersona([
            'codigo' => 'DOC002',
            'email' => 'lucia.vargas@unsxx.bo',
            'rol' => Constantes::ROL_DOCENTE,
            'nombres' => 'Lucía',
            'apellidos' => 'Vargas Rojas',
            'cargo' => 'Docente',
            'carrera' => $informatica,
            'telefono' => '74455667',
            'fecha_ingreso' => '2023-05-03',
            'tipo_contrato' => Constantes::CONTRATO_CONTRATADO,
            'dias_utilizados' => 22,
        ]);

        $this->crearPersona([
            'codigo' => 'DOC003',
            'email' => 'rosa.choque@unsxx.bo',
            'rol' => Constantes::ROL_DOCENTE,
            'nombres' => 'Rosa',
            'apellidos' => 'Choque Huanca',
            'cargo' => 'Docente',
            'carrera' => $enfermeria,
            'telefono' => '70011223',
            'fecha_ingreso' => '2019-09-01',
            'tipo_contrato' => Constantes::CONTRATO_INTERINO,
            'dias_utilizados' => 3,
        ]);

        $this->crearPersona([
            'codigo' => 'DOC004',
            'email' => 'marco.salas@unsxx.bo',
            'rol' => Constantes::ROL_DOCENTE,
            'nombres' => 'Marco',
            'apellidos' => 'Salas Pinto',
            'cargo' => 'Consultor',
            'carrera' => $informatica,
            'telefono' => '70099887',
            'fecha_ingreso' => '2024-02-01',
            'tipo_contrato' => Constantes::CONTRATO_CONSULTOR,
            'dias_utilizados' => 0,
        ]);

        Solicitud::create([
            'docente_id' => $pedro->id,
            'fecha_inicio' => now()->addDays(10)->toDateString(),
            'fecha_fin' => now()->addDays(12)->toDateString(),
            'dias_solicitados' => 3,
            'motivo' => 'Asistencia a un curso de actualización académica en Llallagua.',
            'estado' => Constantes::ESTADO_PENDIENTE,
        ]);
    }

    private function crearPersona(array $datos): Docente
    {
        $rol = Rol::where('codigo', $datos['rol'])->first();

        $usuario = Usuario::create([
            'ci' => $datos['ci'] ?? '00000000',
            'codigo' => $datos['codigo'],
            'email' => $datos['email'],
            'contrasena' => 'Unsxx2026',
            'rol_id' => $rol->id,
            'activo' => true,
            'debe_cambiar_contrasena' => false,
        ]);

        $docente = Docente::create([
            'usuario_id' => $usuario->id,
            'nombres' => $datos['nombres'],
            'apellidos' => $datos['apellidos'],
            'telefono' => $datos['telefono'],
            'cargo' => $datos['cargo'],
            'carrera_id' => $datos['carrera']->id,
            'area_id' => $datos['carrera']->area_id,
            'fecha_ingreso' => $datos['fecha_ingreso'],
            'tipo_contrato' => $datos['tipo_contrato'],
            'dias_utilizados' => 0,
        ]);

        if (($datos['dias_utilizados'] ?? 0) > 0) {
            $docente->dias_utilizados = $datos['dias_utilizados'];
            $docente->saveQuietly();
        }

        return $docente;
    }
}
