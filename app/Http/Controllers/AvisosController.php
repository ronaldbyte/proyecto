<?php

namespace App\Http\Controllers;

use App\Mail\CorreoAviso;
use App\Models\ConfiguracionCorreo;
use App\Services\ServicioCorreo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AvisosController extends Controller
{
    public function editar()
    {
        abort_unless(Auth::user()->esJefatura(), 403);

        return view('jefatura.avisos.editar', [
            'configuracion' => ConfiguracionCorreo::actual(),
        ]);
    }

    public function guardar(Request $request)
    {
        abort_unless(Auth::user()->esJefatura(), 403);

        $datos = $request->validate([
            'usuario' => ['required', 'email', 'max:150'],
            'contrasena' => ['nullable', 'string', 'max:200'],
        ], [
            'usuario.required' => 'Indique el Gmail que usará el sistema para enviar.',
            'usuario.email' => 'Debe ser un correo Gmail válido.',
        ]);

        $configuracion = ConfiguracionCorreo::actual();
        $configuracion->activo = $request->boolean('activo');
        $configuracion->host = 'smtp.gmail.com';
        $configuracion->puerto = $configuracion->puerto ?: 587;
        $configuracion->usuario = $datos['usuario'];
        $configuracion->nombre_remitente = 'Licencias UNSXX';

        if ($request->filled('contrasena')) {
            $configuracion->contrasena = preg_replace('/\s+/', '', $datos['contrasena']);
        }

        $configuracion->ultimo_error = null;
        $configuracion->save();

        return back()->with('exito', 'Configuración guardada.');
    }

    public function probar(ServicioCorreo $servicio)
    {
        abort_unless(Auth::user()->esJefatura(), 403);

        $configuracion = ConfiguracionCorreo::actual();

        if (! $configuracion->estaLista()) {
            return back()->with('error', 'Active los avisos y complete los datos de envío.');
        }

        $servicio->aplicarConfiguracion();
        $destino = Auth::user()->email;

        try {
            Mail::to($destino)->send(new CorreoAviso(
                'Prueba de aviso',
                'Si recibió este mensaje en el celular, los avisos del sistema ya llegan aunque no esté dentro de la página.',
                $servicio->enlaceIngreso()
            ));
            $configuracion->update(['ultimo_error' => null]);
        } catch (\Throwable $excepcion) {
            $puertoAlternativo = ($configuracion->puerto == 465) ? 587 : 465;

            try {
                $servicio->aplicarConfiguracion($puertoAlternativo);
                Mail::to($destino)->send(new CorreoAviso(
                    'Prueba de aviso',
                    'Si recibió este mensaje en el celular, los avisos del sistema ya llegan aunque no esté dentro de la página.',
                    $servicio->enlaceIngreso()
                ));
                $configuracion->update(['puerto' => $puertoAlternativo, 'ultimo_error' => null]);

                return back()->with('exito', 'Se envió una prueba a '.$destino.' (reintentado con éxito por el puerto seguro '.$puertoAlternativo.').');
            } catch (\Throwable) {
                $configuracion->update(['ultimo_error' => $excepcion->getMessage()]);

                return back()->with('error', 'No se pudo enviar la prueba: '.$excepcion->getMessage());
            }
        }

        return back()->with('exito', 'Se envió una prueba a '.$destino.'.');
    }
}
