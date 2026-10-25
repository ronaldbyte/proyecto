<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionCorreo;
use App\Models\Docente;
use App\Models\Solicitud;
use App\Soporte\Constantes;
use Illuminate\Support\Facades\Auth;

class PanelController extends Controller
{
    public function mostrar()
    {
        $usuario = Auth::user()->load(['rol', 'docente.carrera', 'docente.area']);

        if ($usuario->esJefatura()) {
            return $this->jefatura($usuario);
        }

        if ($usuario->esDirector()) {
            return $this->director($usuario);
        }

        return $this->docente($usuario);
    }

    private function docente($usuario)
    {
        $docente = $usuario->docente;

        $resumen = [
            'pendientes' => 0,
            'aprobadas' => 0,
            'rechazadas' => 0,
        ];

        if ($docente) {
            $resumen['pendientes'] = $docente->solicitudes()->whereIn('estado', [
                Constantes::ESTADO_PENDIENTE,
                Constantes::ESTADO_PENDIENTE_AREA,
                Constantes::ESTADO_PENDIENTE_JEFATURA,
            ])->count();
            $resumen['aprobadas'] = $docente->solicitudes()->where('estado', Constantes::ESTADO_APROBADA)->count();
            $resumen['rechazadas'] = $docente->solicitudes()->where('estado', Constantes::ESTADO_RECHAZADA)->count();
        }

        $recientes = $docente
            ? $docente->solicitudes()->latest()->take(5)->get()
            : collect();

        return view('paneles.docente', compact('usuario', 'docente', 'resumen', 'recientes'));
    }

    private function director($usuario)
    {
        $consulta = $this->consultaSolicitudesDirector($usuario);

        $estadosPendientes = $usuario->esDirectorCarrera()
            ? [Constantes::ESTADO_PENDIENTE]
            : [Constantes::ESTADO_PENDIENTE, Constantes::ESTADO_PENDIENTE_AREA];

        $estadosRevisados = [
            Constantes::ESTADO_PENDIENTE_JEFATURA,
            Constantes::ESTADO_APROBADA,
            Constantes::ESTADO_RECHAZADA,
        ];

        $resumen = [
            'pendientes' => (clone $consulta)->whereIn('estado', $estadosPendientes)->count(),
            'revisadas' => (clone $consulta)->whereIn('estado', $estadosRevisados)->count(),
            'aprobadas' => (clone $consulta)->where('estado', Constantes::ESTADO_APROBADA)->count(),
            'rechazadas' => (clone $consulta)->where('estado', Constantes::ESTADO_RECHAZADA)->count(),
        ];

        $pendientes = (clone $consulta)
            ->whereIn('estado', $estadosPendientes)
            ->with('docente.carrera')
            ->latest()
            ->take(8)
            ->get();

        $docente = $usuario->docente;

        return view('paneles.director', compact('usuario', 'docente', 'resumen', 'pendientes'));
    }

    private function jefatura($usuario)
    {
        $consultaDocentes = Docente::whereHas('usuario.rol', fn ($q) => $q->where('codigo', '!=', Constantes::ROL_JEFATURA));

        $totales = [
            'docentes' => (clone $consultaDocentes)->count(),
            'pendientes' => Solicitud::where('estado', Constantes::ESTADO_PENDIENTE)->count(),
            'pendiente_area' => Solicitud::where('estado', Constantes::ESTADO_PENDIENTE_AREA)->count(),
            'pendiente_jefatura' => Solicitud::where('estado', Constantes::ESTADO_PENDIENTE_JEFATURA)->count(),
            'aprobadas' => Solicitud::where('estado', Constantes::ESTADO_APROBADA)->count(),
            'rechazadas' => Solicitud::where('estado', Constantes::ESTADO_RECHAZADA)->count(),
            'dias_utilizados' => (int) (clone $consultaDocentes)->sum('dias_utilizados'),
            'dias_disponibles' => (int) (clone $consultaDocentes)->selectRaw('SUM(CASE WHEN dias_asignados > dias_utilizados THEN dias_asignados - dias_utilizados ELSE 0 END) as total')->value('total'),
            'excedidos' => (clone $consultaDocentes)->whereColumn('dias_utilizados', '>', 'dias_asignados')->count(),
        ];

        $pendientesFinal = Solicitud::with('docente.carrera')
            ->where('estado', Constantes::ESTADO_PENDIENTE_JEFATURA)
            ->latest()
            ->take(8)
            ->get();

        $docentesExcedidos = (clone $consultaDocentes)
            ->with(['carrera', 'usuario'])
            ->whereColumn('dias_utilizados', '>', 'dias_asignados')
            ->take(8)
            ->get();

        $avisosListos = ConfiguracionCorreo::query()->first()?->estaLista() ?? false;

        return view('paneles.jefatura', compact('usuario', 'totales', 'pendientesFinal', 'docentesExcedidos', 'avisosListos'));
    }

    private function consultaSolicitudesDirector($usuario)
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
