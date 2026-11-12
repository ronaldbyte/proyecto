@extends('layouts.impresion')
@section('titulo', 'Listado de solicitudes')
@section('contenido')
<h2>Listado de solicitudes</h2>
<table class="tabla">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Docente</th>
            <th>Código</th>
            <th>Carrera</th>
            <th>Inicio</th>
            <th>Fin</th>
            <th>Días</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @forelse($solicitudes as $solicitud)
            <tr>
                <td>{{ $solicitud->created_at->format('d/m/Y') }}</td>
                <td>{{ $solicitud->docente->nombre_completo }}</td>
                <td>{{ $solicitud->docente->usuario->codigo }}</td>
                <td>{{ $solicitud->docente->carrera->nombre }}</td>
                <td>{{ $solicitud->fecha_inicio->format('d/m/Y') }}</td>
                <td>{{ $solicitud->fecha_fin->format('d/m/Y') }}</td>
                <td>{{ $solicitud->textoDias() }}</td>
                <td>{{ $solicitud->etiquetaEstado() }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 24px; color: #64748b; font-style: italic;">
                    No existen solicitudes registradas para mostrar.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
