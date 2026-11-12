@extends('layouts.impresion')
@section('titulo', 'Historial de Licencias - ' . $docente->nombre_completo)
@section('contenido')

<!-- Encabezado: Solo Nombre del Docente -->
<div style="margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #0b2a5b;">
    <h2 style="font-size: 16px; font-weight: 900; letter-spacing: 0.5px; color: #0b2a5b; margin: 0 0 6px 0; text-transform: uppercase;">
        HISTORIAL DE LICENCIAS
    </h2>
    <div style="font-size: 14px; color: #1e293b; font-weight: 700;">
        Docente: <span style="color: #0b2a5b;">{{ $docente->nombre_completo }}</span>
    </div>
</div>

@php
    $solicitudesHistorial = $solicitudes->whereIn('estado', ['aprobada', 'rechazada']);
@endphp

<!-- Lista Directa de Solicitudes Aprobadas y Rechazadas -->
@if($solicitudesHistorial->isEmpty())
    <div style="padding: 24px; text-align: center; border: 1px dashed #cbd5e1; border-radius: 6px; color: #64748b; font-size: 12px; font-style: italic; background: #f8fafc; margin-top: 10px;">
        El/la docente no cuenta con solicitudes de licencias aprobadas ni rechazadas registradas.
    </div>
@else
<table class="tabla" style="width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 6px;">
    <thead>
        <tr style="background: #f1f5f9; color: #0b2a5b;">
            <th style="padding: 7px 6px; text-align: center; width: 32px; border: 1px solid #cbd5e1;">N°</th>
            <th style="padding: 7px 8px; text-align: center; width: 155px; border: 1px solid #cbd5e1;">Periodo de Licencia</th>
            <th style="padding: 7px 6px; text-align: center; width: 80px; border: 1px solid #cbd5e1;">Días</th>
            <th style="padding: 7px 10px; border: 1px solid #cbd5e1;">Motivo</th>
            <th style="padding: 7px 6px; text-align: center; width: 90px; border: 1px solid #cbd5e1;">Estado</th>
            <th style="padding: 7px 10px; border: 1px solid #cbd5e1; width: 170px;">Decidido por</th>
            <th style="padding: 7px 8px; text-align: center; width: 110px; border: 1px solid #cbd5e1;">Fecha y Hora</th>
        </tr>
    </thead>
    <tbody>
        @foreach($solicitudesHistorial as $index => $solicitud)
            @php
                $decisionFinal = $solicitud->aprobaciones->whereIn('decision', ['aprobada', 'rechazada'])->last()
                    ?? $solicitud->aprobaciones->last();

                $porQuien = '—';
                if ($decisionFinal) {
                    $etiquetaRol = match($decisionFinal->etapa) {
                        'director_carrera' => 'Director de Carrera',
                        'director_area' => 'Director de Área',
                        'jefatura' => 'Jefatura de Personal',
                        default => 'Dirección',
                    };
                    $nombreUsuario = $decisionFinal->usuario?->docente?->nombre_completo;
                    $porQuien = $nombreUsuario ? "{$etiquetaRol} ({$nombreUsuario})" : $etiquetaRol;
                }

                $fechaHora = $decisionFinal?->created_at?->format('d/m/Y H:i')
                    ?? $solicitud->updated_at->format('d/m/Y H:i');
            @endphp
            <tr style="background: {{ $index % 2 === 0 ? '#ffffff' : '#f8fafc' }};">
                <td style="padding: 6px 4px; text-align: center; border: 1px solid #cbd5e1; font-weight: bold;">{{ $index + 1 }}</td>
                <td style="padding: 6px 8px; text-align: center; border: 1px solid #cbd5e1;">
                    <strong>{{ $solicitud->fecha_inicio->format('d/m/Y') }}</strong> al <strong>{{ $solicitud->fecha_fin->format('d/m/Y') }}</strong>
                </td>
                <td style="padding: 6px 6px; text-align: center; border: 1px solid #cbd5e1; font-weight: 600;">
                    {{ $solicitud->textoDias() }}
                </td>
                <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-size: 10.5px;">
                    {{ $solicitud->motivo ?: 'Sin motivo registrado' }}
                </td>
                <td style="padding: 6px 6px; text-align: center; border: 1px solid #cbd5e1;">
                    <span class="estado {{ $solicitud->claseEstado() }}" style="font-size: 10px; padding: 2px 7px;">
                        {{ $solicitud->etiquetaEstado() }}
                    </span>
                </td>
                <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-size: 10.5px;">
                    {{ $porQuien }}
                </td>
                <td style="padding: 6px 8px; text-align: center; border: 1px solid #cbd5e1; font-size: 10.5px; white-space: nowrap;">
                    {{ $fechaHora }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif

<p style="margin-top: 24px; font-size: 10px; color: #64748b; text-align: right;">
    Documento oficial generado el {{ now()->format('d/m/Y H:i') }} · Sistema Web Docentes UNSXX
</p>
@endsection
