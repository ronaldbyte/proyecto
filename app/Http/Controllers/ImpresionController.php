<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Solicitud;
use Illuminate\Support\Facades\Auth;

class ImpresionController extends Controller
{
    public function docente(Docente $docente)
    {
        abort_unless(Auth::user()->esJefatura(), 403);

        $docente->load(['usuario.rol', 'carrera', 'area']);
        $solicitudes = $docente->solicitudes()->with('aprobaciones.usuario')->latest()->get();

        return view('impresion.docente', compact('docente', 'solicitudes'));
    }

    public function solicitudes()
    {
        abort_unless(Auth::user()->esJefatura(), 403);

        $solicitudes = Solicitud::with('docente.carrera', 'docente.usuario', 'aprobaciones.usuario')
            ->latest()
            ->get();

        return view('impresion.solicitudes', compact('solicitudes'));
    }
}
