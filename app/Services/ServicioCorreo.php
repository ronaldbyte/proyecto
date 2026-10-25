<?php

namespace App\Services;

use App\Models\ConfiguracionCorreo;
use Illuminate\Support\Facades\Config;

class ServicioCorreo
{
    public function aplicarConfiguracion(): void
    {
        try {
            $configuracion = ConfiguracionCorreo::query()->first();
        } catch (\Throwable) {
            return;
        }

        if (! $configuracion?->estaLista()) {
            return;
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', 'smtp.gmail.com');
        Config::set('mail.mailers.smtp.port', 587);
        Config::set('mail.mailers.smtp.username', $configuracion->usuario);
        Config::set('mail.mailers.smtp.password', $configuracion->contrasenaPlana());
        Config::set('mail.mailers.smtp.encryption', 'tls');
        Config::set('mail.mailers.smtp.local_domain', 'localhost');
        Config::set('mail.from.address', $configuracion->usuario);
        Config::set('mail.from.name', $configuracion->nombre_remitente ?: 'Licencias UNSXX');

        app()->forgetInstance('mail.manager');
        app()->forgetInstance('mailer');
    }

    public function enlaceIngreso(): string
    {
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
