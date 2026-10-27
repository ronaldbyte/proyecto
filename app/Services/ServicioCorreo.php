<?php

namespace App\Services;

use App\Models\ConfiguracionCorreo;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class ServicioCorreo
{
    public function aplicarConfiguracion(?int $puerto = null): void
    {
        try {
            $configuracion = ConfiguracionCorreo::query()->first();
        } catch (\Throwable) {
            return;
        }

        if (! $configuracion?->estaLista()) {
            return;
        }

        $puertoDestino = $puerto ?? ($configuracion->puerto ?: 587);
        $scheme = ($puertoDestino === 465) ? 'smtps' : 'smtp';
        $encriptacion = ($puertoDestino === 465) ? 'ssl' : 'tls';

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.scheme', $scheme);
        Config::set('mail.mailers.smtp.host', 'smtp.gmail.com');
        Config::set('mail.mailers.smtp.port', $puertoDestino);
        Config::set('mail.mailers.smtp.username', $configuracion->usuario);
        Config::set('mail.mailers.smtp.password', $configuracion->contrasenaPlana());
        Config::set('mail.mailers.smtp.encryption', $encriptacion);
        Config::set('mail.mailers.smtp.timeout', 25);
        Config::set('mail.mailers.smtp.local_domain', 'localhost');
        Config::set('mail.from.address', $configuracion->usuario);
        Config::set('mail.from.name', $configuracion->nombre_remitente ?: 'Licencias UNSXX');

        if (app()->bound('mail.manager')) {
            try {
                Mail::purge('smtp');
            } catch (\Throwable) {
                //
            }
        }

        app()->forgetInstance('mail.manager');
        app()->forgetInstance('mailer');
    }

    public function enlaceIngreso(): string
    {
        if (request()->hasHeader('host')) {
            $hostActual = request()->getSchemeAndHttpHost();
            if (! str_contains($hostActual, '127.0.0.1') && ! str_contains($hostActual, 'localhost')) {
                return rtrim($hostActual, '/').'/ingresar';
            }
        }

        return rtrim((string) config('app.url'), '/').'/ingresar';
    }

    public function puedeEnviar(): bool
    {
        if (app()->runningUnitTests()) {
            return true;
        }

        $this->aplicarConfiguracion();

        return ConfiguracionCorreo::query()->first()?->estaLista() === true;
    }

    public function registrarError(?string $mensaje): void
    {
        try {
            ConfiguracionCorreo::query()->first()?->update(['ultimo_error' => $mensaje]);
        } catch (\Throwable) {
            //
        }
    }
}
