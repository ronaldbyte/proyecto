@extends('layouts.principal')
@section('titulo', 'Jefatura de Personal')
@section('contenido')
@if(! $avisosListos)
    <div class="alerta">Los avisos al celular no están activos. <a href="{{ route('jefatura.avisos') }}">Configurar</a></div>
@endif
<div class="rejilla rejilla-4" style="gap: 12px; margin-bottom: 16px;">
    <div class="tarjeta metrica" style="margin: 0 !important;"><span>Docentes</span><strong>{{ $totales['docentes'] }}</strong></div>
    <div class="tarjeta metrica" style="margin: 0 !important;"><span>Pendientes de Dirección</span><strong>{{ $totales['pendientes'] }}</strong></div>
    <div class="tarjeta metrica" style="margin: 0 !important;"><span>Pendientes de área</span><strong>{{ $totales['pendiente_area'] }}</strong></div>
    <div class="tarjeta metrica" style="margin: 0 !important;"><span>Pendientes de decisión final</span><strong>{{ $totales['pendiente_jefatura'] }}</strong></div>
    <div class="tarjeta metrica" style="margin: 0 !important;"><span>Aprobadas</span><strong>{{ $totales['aprobadas'] }}</strong></div>
    <div class="tarjeta metrica" style="margin: 0 !important;"><span>Rechazadas</span><strong>{{ $totales['rechazadas'] }}</strong></div>
    <div class="tarjeta metrica" style="margin: 0 !important;"><span>Licencias</span><strong>{{ $totales['dias_utilizados'] }}</strong></div>
    <div class="tarjeta metrica" style="margin: 0 !important;"><span>Días disponibles</span><strong>{{ $totales['dias_disponibles'] }}</strong></div>
    <div class="tarjeta metrica excedida" style="margin: 0 !important;"><span>Docentes con días excedidos</span><strong>{{ $totales['excedidos'] }}</strong></div>
</div>

<div class="acciones" style="margin:16px 0;">
    <a class="boton boton-principal" href="{{ route('jefatura.solicitudes.pendientes') }}">Solicitudes pendientes</a>
    <a class="boton boton-azul" href="{{ route('jefatura.docentes.crear') }}">Registrar docente</a>
    <a class="boton boton-suave" href="{{ route('jefatura.docentes.listar') }}">Buscar docentes</a>
</div>

<div class="rejilla rejilla-2" style="gap: 12px; margin-top: 16px;">
    <div class="tarjeta" style="margin: 0 !important;">
        <h3>Pendientes de decisión final</h3>
        @include('parciales.tabla-solicitudes', ['solicitudes' => $pendientesFinal, 'mostrarDocente' => true])
    </div>
    <div class="tarjeta" style="margin: 0 !important;">
        <h3>Docentes con días excedidos</h3>
        @forelse($docentesExcedidos as $item)
            <p>
                <a href="{{ route('jefatura.docentes.ver', $item) }}"><strong>{{ $item->nombre_completo }}</strong></a>
                · {{ $item->carrera->nombre }} · excedidos: {{ $item->dias_excedidos }}
            </p>
        @empty
            <p class="vacio">No hay docentes con días excedidos.</p>
        @endforelse
    </div>
</div>
@endsection
