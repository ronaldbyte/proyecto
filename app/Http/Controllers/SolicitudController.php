<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarSolicitudRequest;
use App\Models\Solicitud;
use App\Services\ServicioSolicitudes;
use App\Services\ServicioVacaciones;
use App\Soporte\Constantes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SolicitudController extends Controller
{
    public function __construct(
        private ServicioSolicitudes $servicio,
        private ServicioVacaciones $vacaciones
    ) {}

    public function mias(Request $request)
    {
        abort_unless(Auth::user()->puedeSolicitarLicencia(), 403);
        $docente = Auth::user()->docente;

        $consulta = $docente->solicitudes()->latest();

        if ($request->filled('estado')) {
            $consulta->where('estado', $request->estado);
        }

        $solicitudes = $consulta->paginate(10)->withQueryString();

        return view('solicitudes.mias', compact('solicitudes', 'docente'));
    }

    public function crear()
    {
        $usuario = Auth::user()->load('docente.carrera', 'docente.area');
        abort_unless($usuario->puedeSolicitarLicencia(), 403);

        return view('solicitudes.crear', [
            'docente' => $usuario->docente,
        ]);
    }

    public function guardar(GuardarSolicitudRequest $request)
    {
        try {
            $this->servicio->crear(Auth::user(), $request->validated());
        } catch (\InvalidArgumentException $excepcion) {
            return back()->withErrors(['fecha_inicio' => $excepcion->getMessage()])->withInput();
        }

        return redirect()->route('solicitudes.mias')->with('exito', 'Solicitud enviada. Quedó en estado pendiente.');
    }

    public function ver(Solicitud $solicitud)
    {
        $usuario = Auth::user();
        $this->autorizarVer($usuario, $solicitud);

        $solicitud->load(['docente.carrera', 'docente.area', 'docente.usuario', 'aprobaciones.usuario']);

        return view('solicitudes.ver', compact('solicitud', 'usuario'));
    }

    public function cancelar(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'motivo_cancelacion' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        try {
            $this->servicio->cancelar($solicitud, Auth::user(), $request->motivo_cancelacion);
        } catch (\InvalidArgumentException $excepcion) {
            return back()->withErrors(['general' => $excepcion->getMessage()]);
        }

        return redirect()->route('solicitudes.mias')->with('exito', 'La solicitud fue cancelada.');
    }

    public function calcularDias(Request $request)
    {
        abort_unless(Auth::user()->puedeSolicitarLicencia(), 403);

        $request->validate([
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date'],
            'jornada' => ['nullable', 'in:completa,manana,tarde'],
        ]);

        $jornada = $request->input('jornada', Constantes::JORNADA_COMPLETA);
        $fechaFin = Constantes::esMedioDia($jornada)
            ? $request->fecha_inicio
            : ($request->fecha_fin ?: $request->fecha_inicio);

        try {
            $dias = $this->vacaciones->calcularDiasSolicitados($request->fecha_inicio, $fechaFin, $jornada);
        } catch (\InvalidArgumentException $excepcion) {
            return response()->json(['error' => $excepcion->getMessage()], 422);
        }

        return response()->json(['dias' => $dias]);
    }

    private function autorizarVer($usuario, Solicitud $solicitud): void
    {
        if ($usuario->esJefatura()) {
            return;
        }

        if ($usuario->docente && $solicitud->docente_id === $usuario->docente->id) {
            return;
        }

        if ($usuario->esDirectorCarrera() && $solicitud->docente->carrera_id === $usuario->docente?->carrera_id) {
            return;
        }

        if ($usuario->esDirectorArea() && $solicitud->docente->area_id === $usuario->docente?->area_id) {
            return;
        }

        abort(403, 'No puede consultar esta solicitud.');
    }
}
