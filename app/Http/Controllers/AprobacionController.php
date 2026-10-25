<?php

namespace App\Http\Controllers;

use App\Http\Requests\DecidirSolicitudRequest;
use App\Models\Solicitud;
use App\Services\ServicioAprobaciones;
use App\Soporte\Constantes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AprobacionController extends Controller
{
    public function __construct(private ServicioAprobaciones $servicio) {}

    public function pendientesDirector()
    {
        $usuario = Auth::user();
        abort_unless($usuario->esDirector(), 403);

        $solicitudes = $this->consultaDirector($usuario)
            ->whereIn('estado', $this->servicio->estadosPendientesDe($usuario))
            ->with('docente.carrera', 'docente.area')
            ->latest()
            ->paginate(12);

        return view('director.pendientes', compact('solicitudes'));
    }

    public function historialDirector(Request $request)
    {
        $usuario = Auth::user();
        abort_unless($usuario->esDirector(), 403);

        $consulta = $this->consultaDirector($usuario)->with('docente.carrera');

        if ($request->filled('estado')) {
            $consulta->where('estado', $request->estado);
        }

        $solicitudes = $consulta->latest()->paginate(12)->withQueryString();

        return view('director.historial', compact('solicitudes'));
    }

    public function decidirDirector(DecidirSolicitudRequest $request, Solicitud $solicitud)
    {
        try {
            $this->servicio->decidirComoDirector(
                $solicitud,
                Auth::user(),
                $request->decision
            );
        } catch (\InvalidArgumentException $excepcion) {
            return back()->withErrors(['general' => $excepcion->getMessage()]);
        }

        $texto = $request->decision === 'aprobada'
            ? 'Solicitud aprobada y enviada a Jefatura de Personal.'
            : 'Solicitud rechazada. El docente será notificado.';

        return redirect()->route('director.pendientes')->with('exito', $texto);
    }

    public function pendientesJefatura()
    {
        abort_unless(Auth::user()->esJefatura(), 403);

        $solicitudes = Solicitud::with('docente.carrera', 'docente.area', 'aprobaciones.usuario')
            ->where('estado', Constantes::ESTADO_PENDIENTE_JEFATURA)
            ->latest()
            ->paginate(12);

        return view('jefatura.solicitudes.pendientes', compact('solicitudes'));
    }

    public function listarJefatura(Request $request)
    {
        abort_unless(Auth::user()->esJefatura(), 403);

        $consulta = Solicitud::with('docente.carrera', 'docente.area', 'docente.usuario');

        if ($request->filled('estado')) {
            $consulta->where('estado', $request->estado);
        }
        if ($request->filled('carrera_id')) {
            $consulta->whereHas('docente', fn ($q) => $q->where('carrera_id', $request->carrera_id));
        }
        if ($request->filled('area_id')) {
            $consulta->whereHas('docente', fn ($q) => $q->where('area_id', $request->area_id));
        }
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $consulta->whereHas('docente', function ($q) use ($buscar) {
                $q->where('nombre_completo', 'like', "%{$buscar}%")
                    ->orWhereHas('usuario', fn ($u) => $u->where('codigo', 'like', "%{$buscar}%"));
            });
        }
        if ($request->filled('desde')) {
            $consulta->whereDate('created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $consulta->whereDate('created_at', '<=', $request->hasta);
        }

        $solicitudes = $consulta->latest()->paginate(15)->withQueryString();

        return view('jefatura.solicitudes.listar', [
            'solicitudes' => $solicitudes,
            'carreras' => \App\Models\Carrera::orderBy('nombre')->get(),
            'areas' => \App\Models\Area::with(['carreras' => fn ($q) => $q->orderBy('nombre')])->orderBy('nombre')->get(),
        ]);
    }

    public function decidirJefatura(DecidirSolicitudRequest $request, Solicitud $solicitud)
    {
        try {
            $this->servicio->decidirComoJefatura(
                $solicitud,
                Auth::user(),
                $request->decision,
                (string) $request->input('observacion', '')
            );
        } catch (\InvalidArgumentException $excepcion) {
            return back()->withErrors(['general' => $excepcion->getMessage()]);
        }

        $texto = $request->decision === 'aprobada'
            ? 'Solicitud aprobada. Se descontaron los días de vacaciones.'
            : 'Solicitud rechazada. No se descontaron días.';

        return redirect()->route('jefatura.solicitudes.pendientes')->with('exito', $texto);
    }

    private function consultaDirector($usuario)
    {
        return Solicitud::query()->whereHas('docente', function ($q) use ($usuario) {
            $q->where('usuario_id', '!=', $usuario->id);
            if ($usuario->esDirectorCarrera()) {
                $q->where('carrera_id', $usuario->docente?->carrera_id);
            }
            if ($usuario->esDirectorArea()) {
                $q->where('area_id', $usuario->docente?->area_id);
            }
        });
    }
}
