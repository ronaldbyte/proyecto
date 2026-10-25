<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    public function listar()
    {
        $notificaciones = Auth::user()
            ->notificaciones()
            ->latest()
            ->paginate(15);

        return view('notificaciones.listar', compact('notificaciones'));
    }

    public function estado()
    {
        $usuario = Auth::user();
        $cantidad = $usuario->notificacionesNoLeidas()->count();
        $ultima = $usuario->notificacionesNoLeidas()->latest('id')->first();

        return response()->json([
            'cantidad' => $cantidad,
            'ultima_id' => $ultima?->id,
            'titulo' => $ultima?->titulo,
        ]);
    }

    public function marcarLeida(Notificacion $notificacion)
    {
        abort_unless($notificacion->usuario_id === Auth::id(), 403);
        $notificacion->marcarLeida();

        if ($notificacion->solicitud_id) {
            return redirect()->route('solicitudes.ver', $notificacion->solicitud_id);
        }

        return back();
    }

    public function marcarTodas()
    {
        Auth::user()->notificaciones()->where('leida', false)->update([
            'leida' => true,
            'leida_en' => now(),
        ]);

        return back()->with('exito', 'Todas las notificaciones fueron marcadas como leídas.');
    }
}
