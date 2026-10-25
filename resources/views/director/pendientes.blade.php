@extends('layouts.principal')
@section('titulo', 'Solicitudes pendientes de dirección')
@section('contenido')
<div class="tarjeta">
    @include('parciales.tabla-solicitudes', ['solicitudes' => $solicitudes, 'mostrarDocente' => true])
    <div class="paginacion">{{ $solicitudes->links() }}</div>
</div>
@endsection
