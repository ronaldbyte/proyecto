<?php

namespace App\Services;

use App\Mail\CorreoAviso;
use App\Models\Notificacion;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Soporte\Constantes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ServicioNotificaciones
{
    public function __construct(private ServicioCorreo $correo) {}

    public function crear(
        Usuario $usuario,
        string $tipo,
        string $titulo,
        string $mensaje,
        ?Solicitud $solicitud = null
    ): Notificacion {
        return Notificacion::create([
            'usuario_id' => $usuario->id,
            'solicitud_id' => $solicitud?->id,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'leida' => false,
        ]);
    }

    public function notificarNuevaSolicitud(Solicitud $solicitud): void
    {
        $solicitud->loadMissing('docente.usuario', 'docente.carrera', 'docente.area');
        $titulo = 'Nueva solicitud de licencia';
        $mensaje = $solicitud->docente->nombre_completo.' presentó una solicitud del '
            .$solicitud->fecha_inicio->format('d/m/Y').' al '.$solicitud->fecha_fin->format('d/m/Y')
            .' ('.$solicitud->textoDias().' día(s)).';

        if ($solicitud->estado === Constantes::ESTADO_PENDIENTE) {
            $destinatarios = $this->directoresCarrera($solicitud)
                ->merge($this->directoresArea($solicitud))
                ->unique('id')
                ->values();
            $this->avisar($destinatarios, 'nueva_solicitud', $titulo, $mensaje, $solicitud);
        } elseif ($solicitud->estado === Constantes::ESTADO_PENDIENTE_AREA) {
            $this->notificarPendienteArea($solicitud);
        } elseif ($solicitud->estado === Constantes::ESTADO_PENDIENTE_JEFATURA) {
            $this->notificarPendienteJefatura($solicitud);
        }
    }

    public function notificarPendienteArea(Solicitud $solicitud): void
    {
        $solicitud->loadMissing('docente.usuario', 'docente.area');
        $titulo = 'Solicitud pendiente de Director de Área';
        $mensaje = 'La solicitud de '.$solicitud->docente->nombre_completo
            .' espera su revisión como Director de Área.';

        $destinatarios = $this->directoresArea($solicitud);

        if ($destinatarios->isEmpty()) {
            Log::warning('No hay Director de Área activo en el área del solicitante.', [
                'solicitud_id' => $solicitud->id,
                'area_id' => $solicitud->docente->area_id,
                'area' => $solicitud->docente->area?->nombre,
            ]);
        }

        $this->avisar($destinatarios, 'pendiente_area', $titulo, $mensaje, $solicitud);
    }

    public function notificarPendienteJefatura(Solicitud $solicitud): void
    {
        $titulo = 'Solicitud pendiente de decisión final';
        $mensaje = 'La solicitud de '.$solicitud->docente->nombre_completo
            .' espera la decisión de Jefatura de Personal.';

        $jefaturas = Usuario::where('activo', true)
            ->whereHas('rol', fn ($q) => $q->where('codigo', Constantes::ROL_JEFATURA))
            ->get();

        $this->avisar($jefaturas, 'pendiente_jefatura', $titulo, $mensaje, $solicitud);
    }

    public function notificarAprobada(Solicitud $solicitud): void
    {
        $usuario = $solicitud->docente->usuario;
        $titulo = 'Solicitud aprobada';
        $mensaje = 'Su solicitud del '.$solicitud->fecha_inicio->format('d/m/Y')
            .' al '.$solicitud->fecha_fin->format('d/m/Y').' fue aprobada por Jefatura de Personal.';

        $this->crear($usuario, 'aprobada', $titulo, $mensaje, $solicitud);
        $this->enviarAviso($usuario, $titulo, $mensaje);
    }

    public function notificarRechazada(Solicitud $solicitud, string $observacion = ''): void
    {
        $usuario = $solicitud->docente->usuario;
        $titulo = 'Solicitud rechazada';
        $mensaje = 'Su solicitud del '.$solicitud->fecha_inicio->format('d/m/Y')
            .' al '.$solicitud->fecha_fin->format('d/m/Y').' fue rechazada.';

        if (filled($observacion)) {
            $mensaje .= ' Motivo: '.$observacion;
        }

        $this->crear($usuario, 'rechazada', $titulo, $mensaje, $solicitud);
        $this->enviarAviso($usuario, $titulo, $mensaje);
    }

    public function directoresCarrera(Solicitud $solicitud): Collection
    {
        $solicitud->loadMissing('docente');

        return Usuario::where('activo', true)
            ->where('id', '!=', $solicitud->docente->usuario_id)
            ->whereHas('rol', fn ($q) => $q->where('codigo', Constantes::ROL_DIRECTOR_CARRERA))
            ->whereHas('docente', fn ($q) => $q->where('carrera_id', $solicitud->docente->carrera_id))
            ->get();
    }

    public function directoresArea(Solicitud $solicitud): Collection
    {
        $solicitud->loadMissing('docente');

        return Usuario::where('activo', true)
            ->where('id', '!=', $solicitud->docente->usuario_id)
            ->whereHas('rol', fn ($q) => $q->where('codigo', Constantes::ROL_DIRECTOR_AREA))
            ->whereHas('docente', fn ($q) => $q->where('area_id', $solicitud->docente->area_id))
            ->get();
    }

    private function avisar(Collection $usuarios, string $tipo, string $titulo, string $mensaje, Solicitud $solicitud): void
    {
        foreach ($usuarios as $usuario) {
            $this->crear($usuario, $tipo, $titulo, $mensaje, $solicitud);
            $this->enviarAviso($usuario, $titulo, $mensaje);
        }
    }

    private function enviarAviso(Usuario $usuario, string $titulo, string $mensaje): void
    {
        $destino = $usuario->email;

        if (! filled($destino) || ! filter_var($destino, FILTER_VALIDATE_EMAIL)) {
            Log::warning('No se envió aviso: el destinatario no tiene un correo válido.', [
                'usuario_id' => $usuario->id,
            ]);

            return;
        }

        if (! $this->correo->puedeEnviar()) {
            return;
        }

        try {
            Mail::mailer('smtp')->to($destino)->send(new CorreoAviso($titulo, $mensaje, $this->correo->enlaceIngreso()));
            $this->correo->registrarError(null);
        } catch (\Throwable $excepcion) {
            try {
                $this->correo->aplicarConfiguracion(465);
                Mail::mailer('smtp')->to($destino)->send(new CorreoAviso($titulo, $mensaje, $this->correo->enlaceIngreso()));
                $this->correo->registrarError(null);
            } catch (\Throwable) {
                $this->correo->registrarError($excepcion->getMessage());
                Log::error('Fallo al enviar aviso al celular.', [
                    'destino' => $destino,
                    'error' => $excepcion->getMessage(),
                ]);
            }
        }
    }
}
