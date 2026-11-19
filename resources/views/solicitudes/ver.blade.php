@extends('layouts.principal')
@section('titulo', 'Detalle de la solicitud')
@section('contenido')
<div class="tarjeta">
    <div class="datos-fijos">
        <div><small>Docente</small><strong>{{ $solicitud->docente->nombre_completo }}</strong></div>
        <div><small>Código</small><strong>{{ $solicitud->docente->usuario->codigo }}</strong></div>
        <div><small>Carrera</small><strong>{{ $solicitud->docente->carrera->nombre }}</strong></div>
        <div><small>Área</small><strong>{{ $solicitud->docente->area->nombre }}</strong></div>
        <div><small>Inicio</small><strong>{{ $solicitud->fecha_inicio->format('d/m/Y') }}</strong></div>
        <div><small>Conclusión</small><strong>{{ $solicitud->fecha_fin->format('d/m/Y') }}</strong></div>
        <div><small>Jornada</small><strong>{{ $solicitud->etiquetaJornada() }}</strong></div>
        <div><small>Días</small><strong>{{ $solicitud->textoDias() }}</strong></div>
        <div><small>Estado</small><strong><span class="estado {{ $solicitud->claseEstado() }}">{{ $solicitud->etiquetaEstado() }}</span></strong></div>
        <div style="grid-column:1/-1;"><small>Motivo</small><strong>{{ $solicitud->motivo }}</strong></div>
        <div><small>Fecha de solicitud</small><strong>{{ $solicitud->created_at->format('d/m/Y H:i') }}</strong></div>
    </div>

    <h3>Historial de aprobaciones</h3>
    @forelse($solicitud->aprobaciones as $aprobacion)
        <p>
            <strong>
                @if($aprobacion->etapa === 'director_carrera')
                    Director de Carrera
                @elseif($aprobacion->etapa === 'director_area')
                    Director de Área
                @elseif($aprobacion->etapa === 'jefatura')
                    Jefatura de Personal
                @else
                    Dirección
                @endif
            </strong>
            · {{ $aprobacion->usuario->nombreVisible() }}
            · {{ strtoupper($aprobacion->decision) }}
            · {{ $aprobacion->created_at->format('d/m/Y H:i') }}
            @if($aprobacion->observacion)
                <br>{{ $aprobacion->observacion }}
            @endif
        </p>
    @empty
        <p class="vacio">Todavía no hay decisiones registradas.</p>
    @endforelse

    @if($solicitud->puedeCancelarsePor($usuario))
        <form method="POST" action="{{ route('solicitudes.cancelar', $solicitud) }}" style="margin-top:16px;">
            @csrf
            <div class="campo">
                <label>Motivo de cancelación</label>
                <textarea name="motivo_cancelacion" required minlength="5"></textarea>
            </div>
            <button class="boton boton-peligro" type="submit">Cancelar solicitud</button>
        </form>
    @endif

    @php
        $servicio = app(\App\Services\ServicioAprobaciones::class);
    @endphp

    @if($usuario->esDirector() && $servicio->puedeRevisarComoDirector($usuario, $solicitud))
        @include('parciales.formulario-decision', ['ruta' => route('director.decidir', $solicitud)])
    @endif

    @if($usuario->esJefatura() && $solicitud->estaPendienteJefatura())
        @if($usuario->codigo === 'JEF001')
            @include('parciales.formulario-decision-jefatura', ['ruta' => route('jefatura.decidir', $solicitud)])
        @else
            <div class="tarjeta" style="margin-top: 1.5rem; background: #eff6ff; border-left: 4px solid #3b82f6; padding: 1.25rem; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="flex: 1; min-width: 250px;">
                    <p style="margin: 0; color: #1e40af; font-weight: 600; font-size: 14px;">
                        La decisión final de aprobación o rechazo está reservada exclusivamente para la Jefatura Titular (JEF001).
                    </p>
                    <p style="margin: 4px 0 0 0; color: #2563eb; font-size: 13px;">
                        Puede enviar un recordatorio directo a la Jefatura Titular si esta solicitud requiere pronta atención.
                    </p>
                </div>
                <form action="{{ route('jefatura.recordar', $solicitud) }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="boton boton-azul" style="display: inline-flex; align-items: center; gap: 6px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        Recordar notificación al Jefe
                    </button>
                </form>
            </div>
        @endif
    @endif
</div>
@endsection
