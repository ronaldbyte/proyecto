@extends('layouts.principal')
@section('titulo', 'Solicitar licencia')
@section('contenido')
<div class="tarjeta">
    <div class="datos-fijos">
        <div><small>Nombre completo</small><strong>{{ $docente->nombre_completo }}</strong></div>
        <div><small>Código</small><strong>{{ $docente->usuario->codigo }}</strong></div>
        <div><small>Carrera</small><strong>{{ $docente->carrera->nombre }}</strong></div>
        <div><small>Área</small><strong>{{ $docente->area->nombre }}</strong></div>
        <div><small>Correo</small><strong>{{ $docente->usuario->email }}</strong></div>
        <div><small>Días disponibles</small><strong>{{ $docente->dias_disponibles }}</strong></div>
    </div>

    @if($docente->tieneDiasExcedidos())
        <div class="alerta">Tiene {{ $docente->dias_excedidos }} día(s) excedido(s).</div>
    @endif

    <form method="POST" action="{{ route('solicitudes.guardar') }}" data-confirmar="¿Confirma el envío de esta solicitud?">
        @csrf
        <div class="campo">
            <label>Jornada</label>
            <div class="opciones-jornada">
                <label>
                    <input type="radio" name="jornada" value="completa" {{ old('jornada', 'completa') === 'completa' ? 'checked' : '' }}>
                    Día(s) completo(s)
                </label>
                <label>
                    <input type="radio" name="jornada" value="manana" {{ old('jornada') === 'manana' ? 'checked' : '' }}>
                    Solo la mañana
                </label>
                <label>
                    <input type="radio" name="jornada" value="tarde" {{ old('jornada') === 'tarde' ? 'checked' : '' }}>
                    Solo la tarde
                </label>
            </div>
        </div>
        <div class="rejilla rejilla-3">
            <div class="campo">
                <label for="fecha_inicio">Fecha de inicio</label>
                <input id="fecha_inicio" type="date" name="fecha_inicio" value="{{ old('fecha_inicio') }}" min="{{ now()->toDateString() }}" required>
            </div>
            <div class="campo" id="campo-fecha-fin">
                <label for="fecha_fin">Fecha de finalización</label>
                <input id="fecha_fin" type="date" name="fecha_fin" value="{{ old('fecha_fin') }}" min="{{ now()->toDateString() }}" required>
            </div>
            <div class="campo">
                <label for="dias_calculados">Cantidad de días</label>
                <input id="dias_calculados" value="—" data-url="{{ route('solicitudes.calcular') }}" data-disponibles="{{ $docente->dias_disponibles }}" readonly>
            </div>
        </div>
        <div id="aviso-exceso" class="alerta" hidden>Los días solicitados superan el saldo disponible.</div>
        <div class="campo">
            <label for="motivo">Motivo / observación</label>
            <textarea id="motivo" name="motivo" rows="4" required minlength="10">{{ old('motivo') }}</textarea>
        </div>
        <button class="boton boton-principal" type="submit">Enviar solicitud</button>
    </form>
</div>
@endsection
