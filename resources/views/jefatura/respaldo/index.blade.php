@extends('layouts.principal')
@section('titulo', 'Copia de seguridad')
@section('subtitulo', 'Respaldo y descarga de la base de datos completa del sistema')
@section('contenido')

<div class="tarjeta" style="margin-bottom:20px;">
    <h3 style="margin-top:0;">Estado de la Base de Datos</h3>
    <p style="color:#555;font-size:14px;margin-bottom:16px;">
        Esta herramienta le permite descargar una copia completa de toda la información del sistema (docentes, cuentas, solicitudes, licencias, historiales y configuraciones). Con este archivo podrá resguardar los datos en su computadora o celular, o migrarlos a cualquier otro servidor cuando lo requiera.
    </p>

    <div class="rejilla rejilla-2" style="margin-bottom:20px;">
        <div style="background:#f8f9fa;padding:14px;border-radius:6px;border:1px solid #e9ecef;">
            <div style="color:#666;font-size:12px;text-transform:uppercase;font-weight:600;">Docentes registrados</div>
            <div style="font-size:24px;font-weight:700;color:#003d80;">{{ $estadisticas['docentes'] }}</div>
        </div>
        <div style="background:#f8f9fa;padding:14px;border-radius:6px;border:1px solid #e9ecef;">
            <div style="color:#666;font-size:12px;text-transform:uppercase;font-weight:600;">Solicitudes de licencias</div>
            <div style="font-size:24px;font-weight:700;color:#003d80;">{{ $estadisticas['solicitudes'] }}</div>
        </div>
        <div style="background:#f8f9fa;padding:14px;border-radius:6px;border:1px solid #e9ecef;">
            <div style="color:#666;font-size:12px;text-transform:uppercase;font-weight:600;">Tamaño de la base de datos</div>
            <div style="font-size:24px;font-weight:700;color:#28a745;">{{ $estadisticas['tamano'] }}</div>
        </div>
        <div style="background:#f8f9fa;padding:14px;border-radius:6px;border:1px solid #e9ecef;">
            <div style="color:#666;font-size:12px;text-transform:uppercase;font-weight:600;">Última modificación</div>
            <div style="font-size:16px;font-weight:600;color:#333;margin-top:4px;">{{ $estadisticas['ultima_modificacion'] }}</div>
        </div>
    </div>

    <a href="{{ route('jefatura.respaldo.descargar') }}" class="boton boton-principal" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;font-size:15px;text-decoration:none;">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Descargar copia de seguridad ahora (.sqlite)
    </a>
</div>

<div class="tarjeta">
    <h3 style="margin-top:0;">Restaurar copia de seguridad</h3>
    <p style="color:#555;font-size:13px;margin-bottom:14px;">
        Si en algún momento necesita restaurar una copia previa o trasladar los datos desde otra instalación, seleccione el archivo <code>.sqlite</code> descargado anteriormente.
    </p>

    <form method="POST" action="{{ route('jefatura.respaldo.restaurar') }}" enctype="multipart/form-data" onsubmit="return confirm('¿Está seguro de restaurar esta copia de seguridad? Se reemplazarán los datos actuales.');">
        @csrf
        <div class="campo" style="max-width:400px;margin-bottom:14px;">
            <label>Archivo de respaldo (.sqlite)</label>
            <input type="file" name="archivo_respaldo" accept=".sqlite,.db" required>
        </div>
        <button type="submit" class="boton boton-azul">Restaurar base de datos</button>
    </form>
</div>

@endsection
