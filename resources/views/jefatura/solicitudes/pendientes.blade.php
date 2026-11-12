@extends('layouts.principal')
@section('titulo', 'Solicitudes')
@section('subtitulo', 'Pendientes de decisión de Jefatura')
@section('contenido')
@include('parciales.pestanas-solicitudes-jefatura')
<div class="tarjeta">
    @include('parciales.tabla-solicitudes', ['solicitudes' => $solicitudes, 'mostrarDocente' => true])
    <div class="paginacion">{{ $solicitudes->links() }}</div>
</div>
@endsection
