<?php

namespace App\Http\Controllers;

use App\Http\Requests\IngresarRequest;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccesoController extends Controller
{
    public function mostrarIngreso()
    {
        return view('acceso.ingresar');
    }

    public function consultarCodigo(Request $request)
    {
        $ci = trim((string) $request->input('ci'));

        if ($ci === '') {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Por favor, ingrese su número de Cédula de Identidad (C.I.).',
            ], 422);
        }

        $usuarios = Usuario::with(['docente.carrera', 'docente.area', 'rol'])
            ->where('ci', $ci)
            ->get();

        if ($usuarios->isEmpty()) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'No se encontró ningún docente registrado con el C.I. ingresado. Verifique su número o consulte con Jefatura de Personal.',
            ], 404);
        }

        $docentes = $usuarios->map(function ($u) {
            return [
                'nombre' => $u->docente?->nombre_completo ?? $u->nombreVisible(),
                'carrera' => $u->docente?->carrera?->nombre ?? ($u->docente?->area?->nombre ?? 'Universidad Nacional Siglo XX'),
                'cargo' => $u->docente?->cargo ?? ($u->rol?->nombre ?? 'Docente'),
                'codigo' => $u->codigo,
                'activo' => (bool) $u->activo,
            ];
        });

        return response()->json([
            'exito' => true,
            'docentes' => $docentes,
        ]);
    }

    public function ingresar(IngresarRequest $request)
    {
        $usuario = Usuario::where('codigo', $request->codigo)->first();

        if (! $usuario || ! Hash::check($request->contrasena, $usuario->contrasena)) {
            return back()->withErrors([
                'codigo' => 'El código o la contraseña no son correctos.',
            ])->onlyInput('codigo');
        }

        if (! $usuario->activo) {
            return back()->withErrors([
                'codigo' => 'Su cuenta está desactivada. Consulte con Jefatura de Personal.',
            ])->onlyInput('codigo');
        }

        Auth::login($usuario, $request->boolean('recordarme'));
        $request->session()->regenerate();

        return redirect()->intended(route('panel'));
    }

    public function salir(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('ingresar')->with('exito', 'Sesión cerrada correctamente.');
    }

    public function editarContrasena()
    {
        return view('acceso.cambiar-contrasena');
    }

    public function actualizarContrasena(Request $request)
    {
        $request->validate([
            'contrasena_actual' => ['required'],
            'contrasena' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'contrasena.confirmed' => 'La confirmación de la contraseña no coincide.',
            'contrasena.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
        ]);

        $usuario = $request->user();

        if (! Hash::check($request->contrasena_actual, $usuario->contrasena)) {
            return back()->withErrors(['contrasena_actual' => 'La contraseña actual no es correcta.']);
        }

        $usuario->contrasena = $request->contrasena;
        $usuario->debe_cambiar_contrasena = false;
        $usuario->save();

        return redirect()->route('panel')->with('exito', 'Contraseña actualizada correctamente.');
    }
}
