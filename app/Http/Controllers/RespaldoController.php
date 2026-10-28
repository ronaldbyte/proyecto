<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Notificacion;
use App\Models\Solicitud;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class RespaldoController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()->esJefatura(), 403);

        $rutaBaseDatos = (string) config('database.connections.sqlite.database');
        $existe = file_exists($rutaBaseDatos);
        $tamanoBytes = $existe ? filesize($rutaBaseDatos) : 0;
        $tamanoFormateado = $this->formatearTamano($tamanoBytes);

        $estadisticas = [
            'docentes' => Docente::count(),
            'usuarios' => Usuario::count(),
            'solicitudes' => Solicitud::count(),
            'notificaciones' => Notificacion::count(),
            'tamano' => $tamanoFormateado,
            'ruta' => $rutaBaseDatos,
            'ultima_modificacion' => $existe ? date('d/m/Y H:i:s', filemtime($rutaBaseDatos)) : 'No disponible',
        ];

        return view('jefatura.respaldo.index', compact('estadisticas'));
    }

    public function descargar()
    {
        abort_unless(Auth::user()->esJefatura(), 403);

        $rutaBaseDatos = (string) config('database.connections.sqlite.database');

        if (! file_exists($rutaBaseDatos)) {
            return back()->with('error', 'El archivo de la base de datos no se encuentra disponible.');
        }

        $nombreArchivo = 'respaldo_licencias_unsxx_'.date('Y-m-d_H-i').'.sqlite';

        return response()->download($rutaBaseDatos, $nombreArchivo, [
            'Content-Type' => 'application/x-sqlite3',
        ]);
    }

    public function restaurar(Request $request)
    {
        abort_unless(Auth::user()->esJefatura(), 403);

        $request->validate([
            'archivo_respaldo' => ['required', 'file', 'max:51200'], // máx 50MB
        ], [
            'archivo_respaldo.required' => 'Seleccione un archivo de respaldo .sqlite para restaurar.',
            'archivo_respaldo.max' => 'El archivo no debe superar los 50 MB.',
        ]);

        $archivo = $request->file('archivo_respaldo');
        $rutaDestino = (string) config('database.connections.sqlite.database');

        // Crear copia de seguridad preventiva antes de sobreescribir
        if (file_exists($rutaDestino)) {
            @copy($rutaDestino, $rutaDestino.'.backup_'.time());
        }

        try {
            File::copy($archivo->getRealPath(), $rutaDestino);
            chmod($rutaDestino, 0777);

            return back()->with('exito', 'Base de datos restaurada correctamente desde el respaldo.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al restaurar el respaldo: '.$e->getMessage());
        }
    }

    private function formatearTamano(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' bytes';
    }
}
