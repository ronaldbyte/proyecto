@extends('layouts.principal')
@section('titulo', 'Avisos al celular')
@section('contenido')
<div class="tarjeta">
    <div style="background:#f0f7ff;border-left:4px solid #0056b3;padding:12px 16px;border-radius:4px;margin-bottom:16px;">
        <h4 style="margin:0 0 6px 0;color:#003d80;">¿Cómo funcionan los avisos al celular o computador?</h4>
        <p style="margin:0 0 8px 0;font-size:13px;line-height:1.5;color:#333;">
            Cuando un docente o director realiza una solicitud o aprobación, el sistema envía una notificación inmediata por correo a la dirección registrada del usuario. Quien tenga su correo abierto en su teléfono (en la app de Gmail) recibirá la notificación de aviso emergente al instante en su pantalla, al igual que en su computador.
        </p>
        <p style="margin:0;font-size:12px;color:#555;line-height:1.4;">
            <strong>Requisito de Google (Gmail):</strong> Google exige una <em>Contraseña de aplicación</em> de 16 caracteres generada en <a href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener" style="color:#0056b3;text-decoration:underline;">myaccount.google.com/apppasswords</a> (requiere tener activa la Verificación en 2 pasos). No use su contraseña normal de inicio de sesión de Google.
        </p>
    </div>

    @if($configuracion->ultimo_error)
        <div class="errores">Último error de envío: {{ $configuracion->ultimo_error }}</div>
    @endif

    <form method="POST" action="{{ route('jefatura.avisos.guardar') }}">
        @csrf
        <label style="display:flex;align-items:center;gap:8px;margin:10px 0;font-weight:600;">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" name="activo" value="1" @checked(old('activo', $configuracion->activo))>
            Activar avisos al celular
        </label>
        <div class="rejilla rejilla-2">
            <div class="campo">
                <label>Gmail del sistema</label>
                <input type="email" name="usuario" value="{{ old('usuario', $configuracion->usuario ?: 'licenciasunsxx@gmail.com') }}" required placeholder="ejemplo@gmail.com">
            </div>
            <div class="campo">
                <label>Contraseña de aplicación (16 letras de Google){{ $configuracion->contrasena ? ' (dejar en blanco para conservar actual)' : '' }}</label>
                <input type="password" name="contrasena" {{ $configuracion->contrasena ? '' : 'required' }} autocomplete="new-password" placeholder="xxxx xxxx xxxx xxxx">
            </div>
        </div>
        <button class="boton boton-principal" type="submit">Guardar configuración</button>
    </form>

    <hr style="margin:24px 0;border:0;border-top:1px solid #e0e0e0;">

    <form method="POST" action="{{ route('jefatura.avisos.probar') }}">
        @csrf
        <h4 style="margin:0 0 8px 0;">Probar envío de aviso al celular / computador</h4>
        <div class="rejilla rejilla-2" style="align-items:end;">
            <div class="campo">
                <label>Enviar correo de prueba a:</label>
                <input type="email" name="correo_prueba" value="{{ old('correo_prueba', auth()->user()->email) }}" placeholder="su-correo@gmail.com" required>
            </div>
            <div class="campo">
                <button class="boton boton-azul" type="submit" style="height:38px;">Enviar prueba ahora</button>
            </div>
        </div>
    </form>
</div>
@endsection
