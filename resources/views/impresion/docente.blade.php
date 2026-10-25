@extends('layouts.impresion')
@section('titulo', 'Historial de Licencias - ' . $docente->nombre_completo)
@section('contenido')

<!-- Título del Reporte -->
<div style="text-align: center; margin-bottom: 18px;">
    <h2 style="font-size: 17px; font-weight: 900; letter-spacing: 0.5px; color: #0b2a5b; margin: 0; text-transform: uppercase; font-family: 'Arial Black', Arial, sans-serif;">
        HISTORIAL DE LICENCIAS Y PERMISOS
    </h2>
    <p style="font-size: 12px; color: #475569; margin: 3px 0 0 0; font-weight: 600;">
        Registro oficial de solicitudes aprobadas y rechazadas
    </p>
</div>

<!-- Datos del Docente -->
<table style="width: 100%; border-collapse: collapse; font-size: 11.5px; margin-bottom: 16px; border: 1px solid #cbd5e1;">
    <tbody>
        <tr style="background-color: #f8fafc;">
            <td style="padding: 6px 10px; font-weight: bold; width: 16%; border: 1px solid #cbd5e1; color: #1e293b;">Docente:</td>
            <td style="padding: 6px 10px; font-weight: bold; width: 34%; border: 1px solid #cbd5e1;">{{ $docente->nombre_completo }}</td>
            <td style="padding: 6px 10px; font-weight: bold; width: 16%; border: 1px solid #cbd5e1; color: #1e293b;">C.I. / Código:</td>
            <td style="padding: 6px 10px; width: 34%; border: 1px solid #cbd5e1;">{{ $docente->usuario->ci ?? '—' }} &nbsp;/&nbsp; <strong>{{ $docente->usuario->codigo }}</strong></td>
        </tr>
        <tr>
            <td style="padding: 6px 10px; font-weight: bold; border: 1px solid #cbd5e1; color: #1e293b;">Carrera:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">{{ $docente->carrera->nombre ?? 'N/A' }}</td>
            <td style="padding: 6px 10px; font-weight: bold; border: 1px solid #cbd5e1; color: #1e293b;">Área:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">{{ $docente->area->nombre ?? 'N/A' }}</td>
        </tr>
        <tr style="background-color: #f8fafc;">
            <td style="padding: 6px 10px; font-weight: bold; border: 1px solid #cbd5e1; color: #1e293b;">Cargo:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">{{ $docente->cargo ?? 'Docente' }}</td>
            <td style="padding: 6px 10px; font-weight: bold; border: 1px solid #cbd5e1; color: #1e293b;">Tipo Contrato:</td>
            <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">{{ $docente->etiquetaContrato() }}</td>
        </tr>
    </tbody>
</table>

@php
    $solicitudesHistorial = $solicitudes->whereIn('estado', ['aprobada', 'rechazada']);
    $totalAprobadas = $solicitudesHistorial->where('estado', 'aprobada')->count();
    $totalRechazadas = $solicitudesHistorial->where('estado', 'rechazada')->count();
    $diasAprobados = $solicitudesHistorial->where('estado', 'aprobada')->sum('dias_solicitados');
@endphp

<!-- Resumen Estadístico Rápido -->
<div style="display: flex; gap: 14px; margin-bottom: 14px; font-size: 11.5px;">
    <div style="padding: 5px 12px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 4px; color: #065f46; font-weight: 600;">
        Licencias aprobadas: <strong>{{ $totalAprobadas }}</strong> ({{ $diasAprobados }} días utilizados)
    </div>
    <div style="padding: 5px 12px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 4px; color: #991b1b; font-weight: 600;">
        Licencias rechazadas: <strong>{{ $totalRechazadas }}</strong>
    </div>
</div>

<!-- Tabla Exclusiva de Solicitudes Aprobadas y Rechazadas -->
@if($solicitudesHistorial->isEmpty())
    <div style="padding: 24px; text-align: center; border: 1px dashed #cbd5e1; border-radius: 6px; color: #64748b; font-size: 12px; font-style: italic; background: #f8fafc; margin-top: 10px;">
        El/la docente no cuenta con solicitudes de licencias aprobadas ni rechazadas registradas en el sistema.
    </div>
@else
<table class="tabla" style="width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 6px;">
    <thead>
        <tr style="background: #f1f5f9; color: #0b2a5b;">
            <th style="padding: 6px 4px; text-align: center; width: 28px; border: 1px solid #cbd5e1;">N°</th>
            <th style="padding: 6px 6px; text-align: center; width: 75px; border: 1px solid #cbd5e1;">F. Solicitud</th>
            <th style="padding: 6px 6px; text-align: center; width: 140px; border: 1px solid #cbd5e1;">Periodo de Licencia</th>
            <th style="padding: 6px 6px; text-align: center; width: 70px; border: 1px solid #cbd5e1;">Días</th>
            <th style="padding: 6px 8px; border: 1px solid #cbd5e1;">Motivo / Justificación</th>
            <th style="padding: 6px 6px; text-align: center; width: 85px; border: 1px solid #cbd5e1;">Estado</th>
            <th style="padding: 6px 6px; text-align: center; width: 90px; border: 1px solid #cbd5e1;">Dir. Carrera</th>
            <th style="padding: 6px 6px; text-align: center; width: 90px; border: 1px solid #cbd5e1;">Dir. Área</th>
            <th style="padding: 6px 6px; text-align: center; width: 90px; border: 1px solid #cbd5e1;">Jefatura</th>
        </tr>
    </thead>
    <tbody>
        @foreach($solicitudesHistorial as $index => $solicitud)
            <tr style="background: {{ $index % 2 === 0 ? '#ffffff' : '#f8fafc' }};">
                <td style="padding: 5px 4px; text-align: center; border: 1px solid #cbd5e1; font-weight: bold;">{{ $index + 1 }}</td>
                <td style="padding: 5px 6px; text-align: center; border: 1px solid #cbd5e1;">{{ $solicitud->created_at->format('d/m/Y') }}</td>
                <td style="padding: 5px 6px; text-align: center; border: 1px solid #cbd5e1;">
                    <strong>{{ $solicitud->fecha_inicio->format('d/m/Y') }}</strong> al <strong>{{ $solicitud->fecha_fin->format('d/m/Y') }}</strong>
                </td>
                <td style="padding: 5px 6px; text-align: center; border: 1px solid #cbd5e1; font-weight: bold;">
                    {{ $solicitud->textoDias() }}
                </td>
                <td style="padding: 5px 8px; border: 1px solid #cbd5e1; font-size: 10.5px;">
                    {{ $solicitud->motivo ?: 'Sin motivo registrado' }}
                </td>
                <td style="padding: 5px 6px; text-align: center; border: 1px solid #cbd5e1;">
                    <span class="estado {{ $solicitud->claseEstado() }}" style="font-size: 10px; padding: 2px 7px;">
                        {{ $solicitud->etiquetaEstado() }}
                    </span>
                </td>
                <td style="padding: 5px 6px; text-align: center; border: 1px solid #cbd5e1; font-size: 10px;">
                    {{ $solicitud->textoDecisionCarrera() }}
                </td>
                <td style="padding: 5px 6px; text-align: center; border: 1px solid #cbd5e1; font-size: 10px;">
                    {{ $solicitud->textoDecisionArea() }}
                </td>
                <td style="padding: 5px 6px; text-align: center; border: 1px solid #cbd5e1; font-size: 10px;">
                    {{ $solicitud->textoDecisionJefatura() }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif

<!-- Firmas de Respaldo Físico -->
<div style="margin-top: 48px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; text-align: center; font-size: 11px;">
    <div>
        <div style="border-top: 1px solid #000; width: 220px; margin: 0 auto; padding-top: 5px;">
            <strong>{{ $docente->nombre_completo }}</strong><br>
            <span style="color: #475569;">Firma del Docente</span>
        </div>
    </div>
    <div>
        <div style="border-top: 1px solid #000; width: 220px; margin: 0 auto; padding-top: 5px;">
            <strong>JEFATURA DE PERSONAL</strong><br>
            <span style="color: #475569;">Firma y Sello</span>
        </div>
    </div>
</div>

<p style="margin-top: 28px; font-size: 10px; color: #64748b; text-align: right;">
    Documento oficial generado el {{ now()->format('d/m/Y H:i') }} · Sistema Web Docentes UNSXX
</p>
@endsection
