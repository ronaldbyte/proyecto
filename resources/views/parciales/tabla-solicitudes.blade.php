@if($solicitudes->count())
<table class="tabla">
    <thead>
        <tr>
            @if(!empty($mostrarDocente))
                <th>Docente</th>
            @endif
            <th>Inicio</th>
            <th>Conclusión</th>
            <th>Días</th>
            <th>Estado</th>
            <th style="text-align: right; width: 1%;">Acción</th>
        </tr>
    </thead>
    <tbody>
        @foreach($solicitudes as $solicitud)
            <tr>
                @if(!empty($mostrarDocente))
                    <td>{{ $solicitud->docente->nombre_completo }}</td>
                @endif
                <td>{{ $solicitud->fecha_inicio->format('d/m/Y') }}</td>
                <td>{{ $solicitud->fecha_fin->format('d/m/Y') }}</td>
                <td>{{ $solicitud->textoDias() }}</td>
                <td><span class="estado {{ $solicitud->claseEstado() }}">{{ $solicitud->etiquetaEstado() }}</span></td>
                <td style="white-space: nowrap; width: 1%; text-align: right;">
                    <div class="acciones-tabla">
                        <a class="boton boton-suave" href="{{ route('solicitudes.ver', $solicitud) }}">Ver</a>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@else
    <p class="vacio">No hay solicitudes para mostrar.</p>
@endif
