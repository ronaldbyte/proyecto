@extends('layouts.principal')
@section('titulo', 'Historial del docente')
@section('contenido')
<div class="acciones no-imprimir" style="margin-bottom:16px;">
    <a class="boton boton-suave" href="{{ route('jefatura.docentes.ver', $docente) }}">Volver a la ficha</a>
    <a class="boton boton-dorado" href="{{ route('jefatura.imprimir.docente', $docente) }}" target="_blank">Imprimir historial de licencias</a>
</div>
@include('parciales.ficha-docente', ['docente' => $docente, 'solicitudes' => $solicitudes])
@endsection
