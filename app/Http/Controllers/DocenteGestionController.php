<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarDocenteRequest;
use App\Models\Area;
use App\Models\Carrera;
use App\Models\Docente;
use App\Models\Rol;
use App\Models\Usuario;
use App\Services\ServicioAuditoria;
use App\Services\ServicioVacaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocenteGestionController extends Controller
{
    public function __construct(
        private ServicioVacaciones $vacaciones,
        private ServicioAuditoria $auditoria
    ) {}

    public function listar(Request $request)
    {
        $consulta = Docente::with(['usuario.rol', 'carrera', 'area']);

        if ($busqueda = trim((string) $request->get('buscar'))) {
            $consulta->where(function ($q) use ($busqueda) {
                $q->where('nombre_completo', 'like', "%{$busqueda}%")
                    ->orWhere('nombres', 'like', "%{$busqueda}%")
                    ->orWhere('apellidos', 'like', "%{$busqueda}%")
                    ->orWhereHas('usuario', fn ($u) => $u->where('codigo', 'like', "%{$busqueda}%"));
            });
        }

        if ($request->filled('carrera_id')) {
            $consulta->where('carrera_id', $request->carrera_id);
        }

        if ($request->filled('area_id')) {
            $consulta->where('area_id', $request->area_id);
        }

        if ($request->filled('rol_id')) {
            $consulta->whereHas('usuario', fn ($u) => $u->where('rol_id', $request->rol_id));
        }

        if ($request->filled('tipo_contrato')) {
            $consulta->where('tipo_contrato', $request->tipo_contrato);
        }

        if ($request->get('excedidos') === '1') {
            $consulta->whereColumn('dias_utilizados', '>', 'dias_asignados');
        }

        $docentes = $consulta
            ->whereHas('usuario.rol', fn ($q) => $q->where('codigo', '!=', \App\Soporte\Constantes::ROL_JEFATURA))
            ->orderBy('apellidos')->paginate(12)->withQueryString();
        $carreras = Carrera::with('area')->orderBy('nombre')->get();
        $areas = Area::with(['carreras' => fn ($q) => $q->orderBy('nombre')])->orderBy('nombre')->get();
        $roles = Rol::where('codigo', '!=', \App\Soporte\Constantes::ROL_JEFATURA)->orderBy('nombre')->get();
        $tiposContrato = \App\Soporte\Constantes::tiposContrato();

        return view('jefatura.docentes.listar', compact('docentes', 'carreras', 'areas', 'roles', 'tiposContrato'));
    }

    public function crear()
    {
        return view('jefatura.docentes.formulario', [
            'docente' => null,
            'codigoGenerado' => $this->codigoEnSesion('codigo_nuevo_docente', \App\Soporte\Constantes::ROL_DOCENTE),
            'carreras' => Carrera::with('area')->orderBy('nombre')->get(),
            'areas' => Area::with(['carreras' => fn ($q) => $q->orderBy('nombre')])->orderBy('nombre')->get(),
            'roles' => Rol::where('codigo', '!=', \App\Soporte\Constantes::ROL_JEFATURA)->orderBy('nombre')->get(),
            'tiposContrato' => \App\Soporte\Constantes::tiposContrato(),
        ]);
    }

    public function sugerirCodigo(Request $request)
    {
        $rol = Rol::find($request->rol_id);
        $codigo = Usuario::generarCodigoUnico($rol?->codigo);
        session(['codigo_nuevo_docente' => $codigo]);

        return response()->json([
            'codigo' => $codigo,
        ]);
    }

    public function guardar(GuardarDocenteRequest $request)
    {
        $carrera = Carrera::findOrFail($request->carrera_id);
        $rol = Rol::findOrFail($request->rol_id);
        abort_if($rol->codigo === \App\Soporte\Constantes::ROL_JEFATURA, 403);

        $codigo = $this->codigoDesdeSesion('codigo_nuevo_docente', $rol->codigo);

        DB::transaction(function () use ($request, $carrera, $codigo) {
            $usuario = Usuario::create([
                'ci' => $request->ci,
                'codigo' => $codigo,
                'email' => $request->email,
                'contrasena' => $request->ci, // Usar CI como contraseña por defecto
                'rol_id' => $request->rol_id,
                'activo' => true,
                'debe_cambiar_contrasena' => true,
            ]);

            $docente = Docente::create([
                'usuario_id' => $usuario->id,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'telefono' => $request->telefono,
                'cargo' => $request->cargo,
                'tipo_contrato' => $request->tipo_contrato,
                'carrera_id' => $carrera->id,
                'area_id' => $carrera->area_id,
                'fecha_ingreso' => $request->fecha_ingreso,
                'dias_utilizados' => 0,
            ]);

            $this->auditoria->registrar('crear_docente', 'docente', $docente->id, null, $docente->toArray());
        });

        session()->forget('codigo_nuevo_docente');

        return redirect()->route('jefatura.docentes.listar')->with('exito', 'Docente registrado. Código de ingreso: '.$codigo);
    }

    public function editar(Docente $docente)
    {
        $this->asegurarNoEsJefatura($docente);
        $docente->load('usuario');

        return view('jefatura.docentes.formulario', [
            'docente' => $docente,
            'carreras' => Carrera::with('area')->orderBy('nombre')->get(),
            'areas' => Area::with(['carreras' => fn ($q) => $q->orderBy('nombre')])->orderBy('nombre')->get(),
            'roles' => Rol::where('codigo', '!=', \App\Soporte\Constantes::ROL_JEFATURA)->orderBy('nombre')->get(),
            'tiposContrato' => \App\Soporte\Constantes::tiposContrato(),
        ]);
    }

    public function actualizar(GuardarDocenteRequest $request, Docente $docente)
    {
        $this->asegurarNoEsJefatura($docente);
        $carrera = Carrera::findOrFail($request->carrera_id);
        $rol = Rol::findOrFail($request->rol_id);
        abort_if($rol->codigo === \App\Soporte\Constantes::ROL_JEFATURA, 403);
        $anterior = $docente->toArray();

        DB::transaction(function () use ($request, $docente, $carrera) {
            $datosUsuario = [
                'ci' => $request->ci,
                'email' => $request->email,
                'rol_id' => $request->rol_id,
                'activo' => $request->boolean('activo'),
            ];

            if ($request->filled('contrasena')) {
                $datosUsuario['contrasena'] = $request->contrasena;
                $datosUsuario['debe_cambiar_contrasena'] = true;
            }

            $docente->usuario->update($datosUsuario);

            $docente->update([
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'telefono' => $request->telefono,
                'cargo' => $request->cargo,
                'tipo_contrato' => $request->tipo_contrato,
                'carrera_id' => $carrera->id,
                'area_id' => $carrera->area_id,
                'fecha_ingreso' => $request->fecha_ingreso,
            ]);

            $this->vacaciones->actualizarDiasUtilizados($docente->fresh());
        });

        $this->auditoria->registrar('actualizar_docente', 'docente', $docente->id, $anterior, $docente->fresh()->toArray());

        return redirect()->route('jefatura.docentes.listar')->with('exito', 'Docente actualizado correctamente.');
    }

    public function desactivar(Docente $docente)
    {
        $this->asegurarNoEsJefatura($docente);
        $docente->usuario->update(['activo' => ! $docente->usuario->activo]);
        $estado = $docente->usuario->activo ? 'activado' : 'desactivado';

        $this->auditoria->registrar('cambiar_estado_docente', 'docente', $docente->id, null, ['activo' => $docente->usuario->activo]);

        return back()->with('exito', "El docente fue {$estado} correctamente.");
    }

    public function ver(Docente $docente)
    {
        $this->asegurarNoEsJefatura($docente);
        $docente->load(['usuario.rol', 'carrera', 'area']);
        $solicitudes = $docente->solicitudes()->with('aprobaciones.usuario')->latest()->get();

        return view('jefatura.docentes.ver', compact('docente', 'solicitudes'));
    }

    private function asegurarNoEsJefatura(Docente $docente): void
    {
        abort_if($docente->usuario?->esJefatura(), 404);
    }

    private function codigoEnSesion(string $clave, string $rolCodigo): string
    {
        $actual = (string) session($clave, '');

        if ($actual !== '' && ! Usuario::where('codigo', $actual)->exists()) {
            return $actual;
        }

        $codigo = Usuario::generarCodigoUnico($rolCodigo);
        session([$clave => $codigo]);

        return $codigo;
    }

    private function codigoDesdeSesion(string $clave, string $rolCodigo): string
    {
        $codigo = (string) session($clave, '');

        if ($codigo !== '' && ! Usuario::where('codigo', $codigo)->exists()) {
            return $codigo;
        }

        return Usuario::generarCodigoUnico($rolCodigo);
    }
}
