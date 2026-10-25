@extends('layouts.principal')
@section('titulo', 'Cambiar contraseña')
@section('contenido')
<div class="tarjeta" style="max-width:480px;">
    <form method="POST" action="{{ route('contrasena.actualizar') }}">
        @csrf
        <div class="campo">
            <label for="contrasena_actual">Contraseña actual</label>
            <input id="contrasena_actual" type="password" name="contrasena_actual" required>
        </div>
        <div class="campo">
            <label for="contrasena">Nueva contraseña</label>
            <input id="contrasena" type="password" name="contrasena" required minlength="8">
        </div>
        <div class="campo">
            <label for="contrasena_confirmation">Confirmar nueva contraseña</label>
            <input id="contrasena_confirmation" type="password" name="contrasena_confirmation" required>
        </div>
        <button class="boton boton-principal" type="submit">Guardar contraseña</button>
    </form>
</div>
@endsection
