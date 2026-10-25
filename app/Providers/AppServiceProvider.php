<?php

namespace App\Providers;

use App\Services\ServicioCorreo;
use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
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

        View::composer('layouts.principal', function ($vista) {
            $usuario = auth()->user();
            $vista->with('cantidadNoLeidas', $usuario ? $usuario->notificacionesNoLeidas()->count() : 0);
        });
    }
}
