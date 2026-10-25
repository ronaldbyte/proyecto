@extends('layouts.principal')
@section('titulo', 'Ficha del docente')
@section('contenido')
<div class="acciones" style="margin-bottom:16px;">
    <a class="boton boton-azul" href="{{ route('jefatura.docentes.editar', $docente) }}">Editar</a>
    <a class="boton boton-dorado" href="{{ route('jefatura.historial.docente', $docente) }}">Historial</a>
    <form method="POST" action="{{ route('jefatura.docentes.estado', $docente) }}" data-confirmar="¿Cambiar el estado de este docente?">
        @csrf
        <button class="boton boton-peligro" type="submit">{{ $docente->usuario->activo ? 'Desactivar' : 'Activar' }}</button>
    </form>
</div>
@include('parciales.ficha-docente', ['docente' => $docente, 'solicitudes' => $solicitudes])
@endsection
