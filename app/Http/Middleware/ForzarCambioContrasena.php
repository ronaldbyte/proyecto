<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForzarCambioContrasena
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if ($usuario && $usuario->debe_cambiar_contrasena) {
            if (! $request->routeIs('contrasena.editar', 'contrasena.actualizar', 'salir')) {
                return redirect()->route('contrasena.editar');
            }
        }

        return $next($request);
    }
}
