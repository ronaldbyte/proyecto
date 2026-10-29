<?php

namespace App\Providers;

use App\Services\ServicioCorreo;
use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->environment('production') || request()->header('X-Forwarded-Proto') === 'https' || str_contains((string) request()->header('host'), 'onrender.com')) {
            URL::forceScheme('https');
        }

        Carbon::setLocale('es');
        Paginator::defaultView('paginacion.simple');

        RateLimiter::for('ingreso', function (Request $request) {
            return Limit::perMinute(5)->by(strtolower($request->ip().'|'.$request->input('codigo', '')));
        });

        try {
            app(ServicioCorreo::class)->aplicarConfiguracion();
        } catch (\Throwable) {
            //
        }

        View::composer(['layouts.principal', 'parciales.menu'], function ($vista) {
            $usuario = auth()->user();
            if (! $usuario) {
                return;
            }

            $cantidadNoLeidas = $usuario->notificacionesNoLeidas()->count();
            $pendientesDirectorCount = 0;
            $pendientesJefaturaCount = 0;
            $todasPendientesJefaturaCount = 0;

            if ($usuario->esDirector()) {
                $carreraId = $usuario->docente?->carrera_id;
                $areaId = $usuario->docente?->area_id;

                $consulta = \App\Models\Solicitud::query()->whereHas('docente', function ($q) use ($usuario, $carreraId, $areaId) {
                    $q->where('usuario_id', '!=', $usuario->id);
                    if ($usuario->esDirectorCarrera()) {
                        $q->where('carrera_id', $carreraId);
                    }
                    if ($usuario->esDirectorArea()) {
                        $q->where('area_id', $areaId);
                    }
                });

                $estados = $usuario->esDirectorCarrera()
                    ? [\App\Soporte\Constantes::ESTADO_PENDIENTE]
                    : [\App\Soporte\Constantes::ESTADO_PENDIENTE, \App\Soporte\Constantes::ESTADO_PENDIENTE_AREA];

                $pendientesDirectorCount = $consulta->whereIn('estado', $estados)->count();
            }

            if ($usuario->esJefatura()) {
                $pendientesJefaturaCount = \App\Models\Solicitud::where('estado', \App\Soporte\Constantes::ESTADO_PENDIENTE_JEFATURA)->count();
                $todasPendientesJefaturaCount = \App\Models\Solicitud::whereIn('estado', [
                    \App\Soporte\Constantes::ESTADO_PENDIENTE,
                    \App\Soporte\Constantes::ESTADO_PENDIENTE_AREA,
                    \App\Soporte\Constantes::ESTADO_PENDIENTE_JEFATURA,
                ])->count();
            }

            $vista->with([
                'cantidadNoLeidas' => $cantidadNoLeidas,
                'pendientesDirectorCount' => $pendientesDirectorCount,
                'pendientesJefaturaCount' => $pendientesJefaturaCount,
                'todasPendientesJefaturaCount' => $todasPendientesJefaturaCount,
            ]);
        });
    }
}
