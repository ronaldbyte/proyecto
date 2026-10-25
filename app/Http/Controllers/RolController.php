<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Support\Facades\Auth;

class RolController extends Controller
{
    public function listar()
    {
        abort_unless(Auth::user()->esJefatura(), 403);

        $roles = Rol::withCount('usuarios')->orderBy('nombre')->get();

        return view('jefatura.roles.listar', compact('roles'));
    }
}
