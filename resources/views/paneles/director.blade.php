@extends('layouts.principal')
@section('titulo', 'Panel de dirección')
@section('subtitulo', $usuario->rol->nombre.' · '.$usuario->nombreVisible())
@section('contenido')
<div class="tarjeta" style="margin-bottom:16px;">
    <div class="acciones">
        <a class="boton boton-principal" href="{{ route('director.pendientes') }}">Revisar pendientes</a>
        @if($usuario->puedeSolicitarLicencia())
            <a class="boton boton-azul" href="{{ route('solicitudes.crear') }}">Solicitar mi licencia</a>
            <a class="boton boton-suave" href="{{ route('historial.propio') }}">Mi historial</a>
        @endif
    </div>
</div>

<div class="rejilla rejilla-4" style="gap: 12px; margin-bottom: 16px;">
    <div class="tarjeta metrica" style="margin: 0 !important;"><span>Pendientes</span><strong>{{ $resumen['pendientes'] }}</strong></div>
    <div class="tarjeta metrica" style="margin: 0 !important;"><span>Revisadas</span><strong>{{ $resumen['revisadas'] }}</strong></div>
    <div class="tarjeta metrica" style="margin: 0 !important;"><span>Aprobadas finales</span><strong>{{ $resumen['aprobadas'] }}</strong></div>
    <div class="tarjeta metrica" style="margin: 0 !important;"><span>Rechazadas</span><strong>{{ $resumen['rechazadas'] }}</strong></div>
</div>

@include('parciales.vacaciones', ['docente' => $docente])

<div class="tarjeta" style="margin-top:16px;">
    <h3>Solicitudes por revisar</h3>
    @include('parciales.tabla-solicitudes', ['solicitudes' => $pendientes, 'mostrarDocente' => true])
</div>
@endsection
