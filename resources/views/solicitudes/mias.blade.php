@extends('layouts.principal')
@section('titulo', 'Mis solicitudes')
@section('contenido')
<form class="filtros" method="GET" style="gap: 12px; margin-bottom: 16px;">
    <select name="estado">
        <option value="">Todos los estados</option>
        <option value="pendiente" @selected(request('estado')==='pendiente')>Pendiente de Dirección</option>
        <option value="pendiente_area" @selected(request('estado')==='pendiente_area')>Pendiente de Área</option>
        <option value="pendiente_jefatura" @selected(request('estado')==='pendiente_jefatura')>Pendiente de Jefatura</option>
        <option value="aprobada" @selected(request('estado')==='aprobada')>Aprobada</option>
        <option value="rechazada" @selected(request('estado')==='rechazada')>Rechazada</option>
        <option value="cancelada" @selected(request('estado')==='cancelada')>Cancelada</option>
    </select>
    <button class="boton boton-azul" type="submit">Filtrar</button>
    <a class="boton boton-principal" href="{{ route('solicitudes.crear') }}">Nueva solicitud</a>
</form>
<div class="tarjeta" style="margin-top: 16px;">
    @include('parciales.tabla-solicitudes', ['solicitudes' => $solicitudes, 'mostrarDocente' => false])
    <div class="paginacion">{{ $solicitudes->links() }}</div>
</div>
@endsection
