@extends('layouts.principal')
@section('titulo', 'Ficha del docente')
@section('contenido')
<div class="acciones" style="margin-bottom:16px;">
    <a class="boton boton-azul" href="{{ route('jefatura.docentes.editar', $docente) }}">Editar</a>
    <a class="boton boton-dorado" href="{{ route('jefatura.historial.docente', $docente) }}">Historial</a>
    <form method="POST" action="{{ route('jefatura.docentes.estado', $docente) }}" data-confirmar="¿Cambiar el estado de este docente?">
        @csrf
        <button class="boton boton-suave" type="submit">{{ $docente->usuario->activo ? 'Desactivar' : 'Activar' }}</button>
    </form>
    <form method="POST" action="{{ route('jefatura.docentes.eliminar', $docente) }}" onsubmit="return confirm('¿Está seguro de eliminar a {{ addslashes($docente->nombre_completo) }} ({{ $docente->usuario->codigo }}) definitivamente de la base de datos?\n\nEsta acción borrará todos sus datos y solicitudes asociadas.')">
        @csrf
        @method('DELETE')
        <button class="boton boton-peligro" type="submit">Eliminar definitivamente</button>
    </form>
</div>
@include('parciales.ficha-docente', ['docente' => $docente, 'solicitudes' => $solicitudes])
@endsection
