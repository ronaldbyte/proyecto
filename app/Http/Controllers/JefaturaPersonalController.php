<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarJefeRequest;
use App\Models\Carrera;
use App\Models\Docente;
use App\Models\Rol;
use App\Models\Usuario;
use App\Services\ServicioAuditoria;
use App\Soporte\Constantes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JefaturaPersonalController extends Controller
{
    public function __construct(private ServicioAuditoria $auditoria) {}

    public function listar()
    {
        abort_unless(Auth::user()->esJefatura(), 403);

        $jefes = Docente::with(['usuario.rol'])
            ->whereHas('usuario.rol', fn ($q) => $q->where('codigo', Constantes::ROL_JEFATURA))
            ->orderBy('apellidos')
            ->get();

        return view('jefatura.personal.listar', compact('jefes'));
    }

    public function crear()
    {
        abort_unless(Auth::user()->esJefatura(), 403);

        $codigo = (string) session('codigo_nuevo_jefe', '');
        if ($codigo === '' || Usuario::where('codigo', $codigo)->exists()) {
            $codigo = Usuario::generarCodigoUnico(Constantes::ROL_JEFATURA);
            session(['codigo_nuevo_jefe' => $codigo]);
        }

        return view('jefatura.personal.formulario', [
            'docente' => null,
            'codigoGenerado' => $codigo,
        ]);
    }

    public function guardar(GuardarJefeRequest $request)
    {
        abort_unless(Auth::user()->esJefatura(), 403);

        $rol = Rol::where('codigo', Constantes::ROL_JEFATURA)->firstOrFail();
        $carrera = Carrera::with('area')->orderBy('id')->firstOrFail();
        $codigo = (string) session('codigo_nuevo_jefe', '');
        if ($codigo === '' || Usuario::where('codigo', $codigo)->exists()) {
            $codigo = Usuario::generarCodigoUnico(Constantes::ROL_JEFATURA);
        }

        DB::transaction(function () use ($request, $rol, $carrera, $codigo) {
            $usuario = Usuario::create([
                'ci' => $request->ci,
                'codigo' => $codigo,
                'email' => $request->email,
                'contrasena' => $request->ci, // Usar CI como contraseña por defecto
                'rol_id' => $rol->id,
                'activo' => true,
                'debe_cambiar_contrasena' => true,
            ]);

            $docente = Docente::create([
                'usuario_id' => $usuario->id,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'telefono' => $request->telefono,
                'cargo' => $request->cargo,
                'tipo_contrato' => Constantes::CONTRATO_CONSULTOR,
                'carrera_id' => $carrera->id,
                'area_id' => $carrera->area_id,
                'fecha_ingreso' => now()->toDateString(),
                'dias_utilizados' => 0,
            ]);

            $this->auditoria->registrar('crear_jefatura', 'docente', $docente->id, null, $docente->toArray());
        });

        session()->forget('codigo_nuevo_jefe');

        return redirect()->route('jefatura.personal.listar')->with('exito', 'Jefe de personal registrado. Código de ingreso: '.$codigo);
    }

    public function editar(Docente $docente)
    {
        abort_unless(Auth::user()->esJefatura(), 403);
        abort_unless($docente->usuario?->esJefatura(), 404);
        $docente->load('usuario');

        return view('jefatura.personal.formulario', compact('docente'));
    }

    public function actualizar(GuardarJefeRequest $request, Docente $docente)
    {
        abort_unless(Auth::user()->esJefatura(), 403);
        abort_unless($docente->usuario?->esJefatura(), 404);

        $anterior = $docente->toArray();
        $rol = Rol::where('codigo', Constantes::ROL_JEFATURA)->firstOrFail();

        DB::transaction(function () use ($request, $docente, $rol) {
            $datosUsuario = [
                'ci' => $request->ci,
                'email' => $request->email,
                'rol_id' => $rol->id,
                'activo' => $request->boolean('activo'),
            ];

            if ($request->filled('contrasena')) {
                $datosUsuario['contrasena'] = $request->contrasena;
                $datosUsuario['debe_cambiar_contrasena'] = true;
            }

            if ($docente->usuario_id === Auth::id()) {
                $datosUsuario['activo'] = true;
            }

            $docente->usuario->update($datosUsuario);
            $docente->update([
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'telefono' => $request->telefono,
                'cargo' => $request->cargo,
            ]);
        });

        $this->auditoria->registrar('actualizar_jefatura', 'docente', $docente->id, $anterior, $docente->fresh()->toArray());

        return redirect()->route('jefatura.personal.listar')->with('exito', 'Jefe de personal actualizado correctamente.');
    }
}
