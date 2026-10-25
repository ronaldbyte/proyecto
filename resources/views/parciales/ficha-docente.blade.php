<div class="tarjeta">
    <div class="datos-fijos">
        <div><small>Código</small><strong>{{ $docente->usuario->codigo }}</strong></div>
        <div><small>Nombre completo</small><strong>{{ $docente->nombre_completo }}</strong></div>
        <div><small>Correo</small><strong>{{ $docente->usuario->email }}</strong></div>
        <div><small>Teléfono</small><strong>{{ $docente->telefono ?: '—' }}</strong></div>
        <div><small>Carrera</small><strong>{{ $docente->carrera->nombre }}</strong></div>
        <div><small>Área</small><strong>{{ $docente->area->nombre }}</strong></div>
        <div><small>Cargo</small><strong>{{ $docente->cargo }}</strong></div>
        <div><small>Rol</small><strong>{{ $docente->usuario->rol->nombre }}</strong></div>
        <div><small>Tipo de contrato</small><strong>{{ $docente->etiquetaContrato() }}</strong></div>
        <div><small>Fecha de ingreso</small><strong>{{ $docente->fecha_ingreso->format('d/m/Y') }}</strong></div>
        <div><small>Vigencia del contrato</small><strong>{{ $docente->vigenciaContratoTexto() ?: '—' }}</strong></div>
        <div><small>Antigüedad</small><strong>{{ $docente->antiguedad_anios }} año(s)</strong></div>
        <div><small>Estado</small><strong>{{ $docente->usuario->activo ? 'Activo' : 'Inactivo' }}</strong></div>
    </div>
    @include('parciales.vacaciones', ['docente' => $docente])

    @php
        $aprobadas = $solicitudes->where('estado', 'aprobada');
        $rechazadas = $solicitudes->where('estado', 'rechazada');
        $pendientes = $solicitudes->whereIn('estado', ['pendiente', 'pendiente_area', 'pendiente_jefatura']);
    @endphp

    <div class="no-imprimir">
        <h3 style="margin-top:20px;">Solicitudes pendientes</h3>
        @include('parciales.tabla-solicitudes', ['solicitudes' => $pendientes, 'mostrarDocente' => false])
        <h3 style="margin-top:20px;">Solicitudes aprobadas</h3>
        @include('parciales.tabla-solicitudes', ['solicitudes' => $aprobadas, 'mostrarDocente' => false])
        <h3 style="margin-top:20px;">Solicitudes rechazadas</h3>
        @include('parciales.tabla-solicitudes', ['solicitudes' => $rechazadas, 'mostrarDocente' => false])
    </div>
</div>
