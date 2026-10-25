@extends('layouts.principal')
@section('titulo', 'Todas las solicitudes')
@section('contenido')
<form class="filtros" method="GET">
    <input name="buscar" value="{{ request('buscar') }}" placeholder="Docente o código">
    <select name="estado">
        <option value="">Estado</option>
        <option value="pendiente" @selected(request('estado')==='pendiente')>Pendiente de Carrera</option>
        <option value="pendiente_area" @selected(request('estado')==='pendiente_area')>Pendiente de Área</option>
        <option value="pendiente_jefatura" @selected(request('estado')==='pendiente_jefatura')>Pendiente de Jefatura</option>
        <option value="aprobada" @selected(request('estado')==='aprobada')>Aprobada</option>
        <option value="rechazada" @selected(request('estado')==='rechazada')>Rechazada</option>
        <option value="cancelada" @selected(request('estado')==='cancelada')>Cancelada</option>
    </select>
    <select name="carrera_id">
        @include('parciales.opciones-carreras', [
            'areasCatalogo' => $areas,
            'seleccionada' => request('carrera_id'),
            'textoVacio' => 'Carrera',
        ])
    </select>
    <select name="area_id">
        <option value="">Área</option>
        @foreach($areas as $area)
            <option value="{{ $area->id }}" @selected(request('area_id')==$area->id)>{{ $area->nombre }}</option>
        @endforeach
    </select>
    <input type="date" name="desde" value="{{ request('desde') }}">
    <input type="date" name="hasta" value="{{ request('hasta') }}">
    <button class="boton boton-azul" type="submit">Filtrar</button>
    <a class="boton boton-suave" href="{{ route('jefatura.imprimir.solicitudes') }}" target="_blank">Imprimir</a>
</form>
<div class="tarjeta">
    @include('parciales.tabla-solicitudes', ['solicitudes' => $solicitudes, 'mostrarDocente' => true])
    <div class="paginacion">{{ $solicitudes->links() }}</div>
</div>
@endsection
