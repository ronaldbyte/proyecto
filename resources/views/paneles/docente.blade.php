@extends('layouts.principal')
@section('titulo', 'Panel del docente')
@section('subtitulo', 'Bienvenido(a), '.$usuario->nombreVisible())
@section('contenido')
<div class="tarjeta" style="margin-bottom:16px;">
    <div class="datos-fijos">
        <div><small>Nombre completo</small><strong>{{ $usuario->nombreVisible() }}</strong></div>
        <div><small>Código</small><strong>{{ $usuario->codigo }}</strong></div>
        <div><small>Carrera</small><strong>{{ $docente?->carrera?->nombre ?? '—' }}</strong></div>
        <div><small>Área</small><strong>{{ $docente?->area?->nombre ?? '—' }}</strong></div>
        <div><small>Contrato</small><strong>{{ $docente?->etiquetaContrato() ?? '—' }}</strong></div>
    </div>
    @if($usuario->puedeSolicitarLicencia())
        <div class="acciones">
            <a class="boton boton-principal" href="{{ route('solicitudes.crear') }}">Solicitar licencia</a>
            <a class="boton boton-suave" href="{{ route('solicitudes.mias') }}">Ver historial</a>
            <a class="boton boton-suave" href="{{ route('notificaciones.listar') }}">Notificaciones</a>
        </div>
    @else
        <div class="alerta">No puede solicitar licencias.</div>
    @endif
</div>

@if($usuario->puedeSolicitarLicencia())
    @include('parciales.vacaciones', ['docente' => $docente])

    <div class="rejilla rejilla-3" style="margin-top:16px;">
        <div class="tarjeta metrica"><span>Pendientes</span><strong>{{ $resumen['pendientes'] }}</strong></div>
        <div class="tarjeta metrica"><span>Aprobadas</span><strong>{{ $resumen['aprobadas'] }}</strong></div>
        <div class="tarjeta metrica"><span>Rechazadas</span><strong>{{ $resumen['rechazadas'] }}</strong></div>
    </div>

    <div class="tarjeta" style="margin-top:16px;">
        <h3>Solicitudes recientes</h3>
        @include('parciales.tabla-solicitudes', ['solicitudes' => $recientes, 'mostrarDocente' => false])
    </div>
@endif
@endsection
