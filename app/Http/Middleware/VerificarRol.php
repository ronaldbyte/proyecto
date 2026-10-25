<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        if (! $usuario || ! $usuario->activo) {
            abort(403, 'No tiene autorización para acceder a esta sección.');
        }

        if (! in_array($usuario->codigoRol(), $roles, true)) {
            abort(403, 'No tiene autorización para acceder a esta sección.');
        }

        return $next($request);
    }
}
