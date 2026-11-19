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
            <div class="tarjeta" style="margin-top: 1.5rem; background: #eff6ff; border-left: 4px solid #3b82f6; padding: 1rem; border-radius: 6px;">
                <p style="margin: 0; color: #1e40af; font-weight: 500;">
                    <strong>Información:</strong> La decisión final de aprobación o rechazo de esta solicitud está reservada exclusivamente para la Jefatura de Personal Titular (JEF001). Usted puede revisar los antecedentes y detalles de la solicitud con fines informativos.
                </p>
            </div>
        @endif
    @endif
</div>
@endsection
