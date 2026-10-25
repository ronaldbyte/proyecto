<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use Illuminate\Support\Facades\Auth;

class HistorialController extends Controller
{
    public function propio()
    {
        abort_unless(Auth::user()->puedeSolicitarLicencia(), 403);
        $docente = Auth::user()->docente;

        $docente->load(['usuario.rol', 'carrera', 'area']);
        $solicitudes = $docente->solicitudes()->with('aprobaciones.usuario')->latest()->get();

        return view('historial.propio', compact('docente', 'solicitudes'));
    }

    public function docente(Docente $docente)
    {
        abort_unless(Auth::user()->esJefatura(), 403);

        $docente->load(['usuario.rol', 'carrera', 'area']);
        $solicitudes = $docente->solicitudes()->with('aprobaciones.usuario')->latest()->get();

        return view('jefatura.historial.docente', compact('docente', 'solicitudes'));
    }
}
