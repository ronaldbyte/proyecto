@extends('layouts.principal')
@section('titulo', 'Copia de seguridad')
@section('contenido')

<div class="tarjeta" style="margin-bottom:20px;">
    <h3 style="margin-top:0;margin-bottom:16px;">Descargar copia de seguridad</h3>
    <a href="{{ route('jefatura.respaldo.descargar') }}" class="boton boton-principal" style="display:inline-flex;align-items:center;gap:8px;">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Descargar copia de seguridad
    </a>
</div>

<div class="tarjeta">
    <h3 style="margin-top:0;margin-bottom:16px;">Restaurar copia de seguridad</h3>
    <form method="POST" action="{{ route('jefatura.respaldo.restaurar') }}" enctype="multipart/form-data" onsubmit="return confirm('¿Está seguro de restaurar esta copia de seguridad? Se reemplazarán los datos actuales.');">
        @csrf
        <div class="campo" style="max-width:400px;margin-bottom:16px;">
            <label>Archivo de respaldo</label>
            <input type="file" name="archivo_respaldo" required>
        </div>
        <button type="submit" class="boton boton-azul">Restaurar copia de seguridad</button>
    </form>
</div>

<div class="tarjeta" style="margin-top:24px; border:1px solid #fecaca; background:#fff7f7;">
    <h3 style="margin-top:0;margin-bottom:10px; color:var(--rojo-unsxx);">Limpiar datos de prueba (Puesta en marcha)</h3>
    <p style="font-size:13.5px; color:#475569; margin:0 0 16px; line-height:1.45;">
        Esta acción elimina permanentemente todos los docentes, directores y solicitudes de prueba cargados en el sistema, dejando <strong>únicamente la cuenta de Jefatura de Personal</strong> (y el catálogo oficial de carreras/áreas) para que pueda iniciar el registro oficial desde cero.
    </p>
    <form method="POST" action="{{ route('jefatura.respaldo.limpiar') }}" onsubmit="return confirm('¿CONFIRMA LIMPIAR LA BASE DE DATOS?\n\nSe eliminarán todos los docentes, directores y solicitudes de prueba.\nÚnicamente se mantendrá la cuenta de Jefatura de Personal para comenzar el registro oficial.');">
        @csrf
        <button type="submit" class="boton boton-peligro" style="font-weight:700;">
            🧹 Limpiar sistema y dejar solo Jefatura
        </button>
    </form>
</div>

@endsection
