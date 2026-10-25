@extends('layouts.principal')
@section('titulo', $docente ? 'Editar jefe de personal' : 'Registrar jefe de personal')
@section('contenido')
<div class="tarjeta">
    <form method="POST" action="{{ $docente ? route('jefatura.personal.actualizar', $docente) : route('jefatura.personal.guardar') }}">
        @csrf
        @if($docente) @method('PUT') @endif
        <div class="rejilla rejilla-2">
            <div class="campo campo-codigo">
                <label>Código de ingreso</label>
                <input name="codigo" value="{{ old('codigo', $docente->usuario->codigo ?? $codigoGenerado ?? '') }}" readonly>
            </div>
            <div class="campo">
                <label>Carnet de Identidad (CI)</label>
                <input name="ci" value="{{ old('ci', $docente->usuario->ci ?? '') }}" required>
            </div>
            <div class="campo">
                <label>Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email', $docente->usuario->email ?? '') }}" required>
            </div>
            <div class="campo">
                <label>Nombres</label>
                <input name="nombres" value="{{ old('nombres', $docente->nombres ?? '') }}" required>
            </div>
            <div class="campo">
                <label>Apellidos</label>
                <input name="apellidos" value="{{ old('apellidos', $docente->apellidos ?? '') }}" required>
            </div>
            <div class="campo">
                <label>Teléfono</label>
                <input name="telefono" value="{{ old('telefono', $docente->telefono ?? '') }}">
            </div>
            <div class="campo">
                <label>Cargo</label>
                <input name="cargo" value="{{ old('cargo', $docente->cargo ?? 'Jefe de Personal') }}" required>
            </div>
            @if($docente)
            <div class="campo">
                <label>Contraseña (dejar en blanco para no cambiar)</label>
                <input type="password" name="contrasena" minlength="5" autocomplete="new-password">
            </div>
            @endif
            @if($docente)
                <div class="campo">
                    <label style="display:flex;gap:8px;align-items:center;margin-top:24px;">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" name="activo" value="1" @checked(old('activo', $docente->usuario->activo))>
                        Activo
                    </label>
                </div>
            @endif
        </div>
        <button class="boton boton-principal" type="submit">Guardar</button>
        <a class="boton boton-suave" href="{{ route('jefatura.personal.listar') }}">Cancelar</a>
    </form>
</div>
@endsection
