@extends('layouts.principal')
@section('titulo', 'Avisos al celular')
@section('contenido')
<div class="tarjeta">
    @if($configuracion->ultimo_error)
        <div class="errores">Último error de envío: {{ $configuracion->ultimo_error }}</div>
    @endif

    <form method="POST" action="{{ route('jefatura.avisos.guardar') }}">
        @csrf
        <label style="display:flex;gap:8px;margin:10px 0;">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" name="activo" value="1" @checked(old('activo', $configuracion->activo))>
            Activar avisos al celular
        </label>
        <div class="rejilla rejilla-2">
            <div class="campo">
                <label>Gmail del sistema</label>
                <input type="email" name="usuario" value="{{ old('usuario', $configuracion->usuario) }}" required>
            </div>
            <div class="campo">
                <label>Contraseña de aplicación{{ $configuracion->contrasena ? ' (dejar en blanco para no cambiar)' : '' }}</label>
                <input type="password" name="contrasena" {{ $configuracion->contrasena ? '' : 'required' }} autocomplete="new-password">
            </div>
        </div>
        <button class="boton boton-principal" type="submit">Guardar</button>
    </form>

    <form method="POST" action="{{ route('jefatura.avisos.probar') }}" style="margin-top:20px;">
        @csrf
        <div class="rejilla rejilla-2" style="align-items:end;">
            <div class="campo">
                <label>Enviar prueba a</label>
                <input type="email" name="correo_prueba" value="{{ old('correo_prueba', auth()->user()->email) }}" required>
            </div>
            <div class="campo">
                <button class="boton boton-azul" type="submit">Enviar prueba</button>
            </div>
        </div>
    </form>
</div>
@endsection
