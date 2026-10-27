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

    public function probar(Request $request, ServicioCorreo $servicio)
    {
        abort_unless(Auth::user()->esJefatura(), 403);

        $configuracion = ConfiguracionCorreo::actual();

        if (! $configuracion->estaLista()) {
            return back()->with('error', 'Active los avisos y guarde los datos de envío (Gmail y Contraseña de aplicación) antes de probar.');
        }

        $destino = trim((string) $request->input('correo_prueba'));
        if ($destino === '') {
            $destino = (string) Auth::user()->email;
        }

        if (! filter_var($destino, FILTER_VALIDATE_EMAIL)) {
            return back()->with('error', 'Debe indicar un correo electrónico válido para recibir la prueba.');
        }

        $servicio->aplicarConfiguracion();

        try {
            Mail::mailer('smtp')->to($destino)->send(new CorreoAviso(
                'Prueba de aviso UNSXX',
                'Si recibió este mensaje en su celular o computador, los avisos del sistema ya funcionan correctamente.',
                $servicio->enlaceIngreso()
            ));
            $configuracion->update(['ultimo_error' => null]);
        } catch (\Throwable $excepcion) {
            $puertoAlternativo = ($configuracion->puerto == 465) ? 587 : 465;

            try {
                $servicio->aplicarConfiguracion($puertoAlternativo);
                Mail::mailer('smtp')->to($destino)->send(new CorreoAviso(
                    'Prueba de aviso UNSXX',
                    'Si recibió este mensaje en su celular o computador, los avisos del sistema ya funcionan correctamente.',
                    $servicio->enlaceIngreso()
                ));
                $configuracion->update(['puerto' => $puertoAlternativo, 'ultimo_error' => null]);

                return back()->with('exito', 'Se envió una prueba exitosa a '.$destino.' (reintentado con éxito por el puerto seguro '.$puertoAlternativo.').');
            } catch (\Throwable) {
                $mensajeError = $excepcion->getMessage();
                $configuracion->update(['ultimo_error' => $mensajeError]);

                $sugerencia = '';
                if (str_contains($mensajeError, '535') || str_contains($mensajeError, 'BadCredentials') || str_contains(strtolower($mensajeError), 'password not accepted')) {
                    $sugerencia = ' (Google rechazó la contraseña. Verifique que esté usando una "Contraseña de aplicación" de 16 caracteres de Google y no su contraseña normal de Gmail).';
                }

                return back()->with('error', 'No se pudo enviar la prueba: '.$mensajeError.$sugerencia);
            }
        }

        return back()->with('exito', '¡Prueba enviada con éxito a '.$destino.'! Revise su bandeja de entrada o spam en su celular o computador.');
    }
}
