@extends('layouts.principal')
@section('titulo', 'Mi historial')
@section('contenido')
<div class="acciones no-imprimir" style="margin-bottom:16px;">
    <a class="boton boton-dorado" href="{{ route('historial.imprimir') }}" target="_blank">
        Imprimir historial de licencias
    </a>
</div>
@include('parciales.ficha-docente', ['docente' => $docente, 'solicitudes' => $solicitudes])
@endsection
