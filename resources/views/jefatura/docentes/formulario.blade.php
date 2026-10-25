@extends('layouts.principal')
@section('titulo', $docente ? 'Editar docente' : 'Registrar docente')
@section('contenido')
<div class="tarjeta">
    <form method="POST" action="{{ $docente ? route('jefatura.docentes.actualizar', $docente) : route('jefatura.docentes.guardar') }}">
        @csrf
        @if($docente) @method('PUT') @endif
        <div class="rejilla rejilla-2">
            <div class="campo campo-codigo">
                <label>Código de ingreso</label>
                <input
                    id="codigo_asignado"
                    name="codigo"
                    value="{{ old('codigo', $docente->usuario->codigo ?? $codigoGenerado ?? '') }}"
                    data-url="{{ route('jefatura.docentes.codigo') }}"
                    @if($docente) data-fijo="1" @endif
                    readonly
                >
            </div>
            <div class="campo">
                <label>Carnet de Identidad (CI)</label>
                <input name="ci" value="{{ old('ci', $docente->usuario->ci ?? '') }}" required>
            </div>
            <div class="campo">
                <label>Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email', $docente->usuario->email ?? '') }}" required>
            </div>
            <div class="campo">
                <label>Nombres</label>
                <input name="nombres" value="{{ old('nombres', $docente->nombres ?? '') }}" required>
            </div>
            <div class="campo">
                <label>Apellidos</label>
                <input name="apellidos" value="{{ old('apellidos', $docente->apellidos ?? '') }}" required>
            </div>
            <div class="campo">
                <label>Teléfono</label>
                <input name="telefono" value="{{ old('telefono', $docente->telefono ?? '') }}">
            </div>
            <div class="campo">
                <label>Cargo</label>
                <input name="cargo" value="{{ old('cargo', $docente->cargo ?? '') }}" required>
            </div>
            <div class="campo">
                <label>Tipo de contrato</label>
                <select name="tipo_contrato" id="tipo_contrato" required>
                    <option value="">Seleccione</option>
                    @foreach($tiposContrato as $codigo => $nombre)
                        <option value="{{ $codigo }}" @selected(old('tipo_contrato', $docente->tipo_contrato ?? '')==$codigo)>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="campo">
                <label>Área</label>
                <select id="area_id" required>
                    <option value="">Seleccione el área</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" @selected(old('area_id', $docente->area_id ?? '')==$area->id)>{{ $area->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="campo">
                <label>Carrera</label>
                <select name="carrera_id" id="carrera_id" required>
                    @include('parciales.opciones-carreras', [
                        'areasCatalogo' => $areas,
                        'seleccionada' => old('carrera_id', $docente->carrera_id ?? ''),
                    ])
                </select>
            </div>
            <div class="campo">
                <label>Fecha de ingreso</label>
                <input id="fecha_ingreso" type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso', isset($docente) ? $docente->fecha_ingreso->format('Y-m-d') : '') }}" required>
            </div>
            <div class="campo">
                <label>Antigüedad y días asignados</label>
                <input id="dias_asignados_vista" value="—" readonly>
            </div>
            <div class="campo">
                <label>Vigencia del contrato actual</label>
                <input id="vigencia_contrato" value="—" readonly>
            </div>
            <div class="campo">
                <label>Rol</label>
                <select name="rol_id" id="rol_id" required>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}" data-codigo="{{ $rol->codigo }}" @selected(old('rol_id', $docente->usuario->rol_id ?? '')==$rol->id)>{{ $rol->nombre }}</option>
                    @endforeach
                </select>
            </div>
            @if($docente)
            <div class="campo">
                <label>Contraseña (dejar en blanco para no cambiar)</label>
                <input type="password" name="contrasena" minlength="5">
            </div>
            @endif
        </div>
        @if($docente)
            <label style="display:flex;gap:8px;margin:10px 0;">
                <input type="hidden" name="activo" value="0">
                <input type="checkbox" name="activo" value="1" @checked(old('activo', $docente->usuario->activo))>
                Docente activo
            </label>
        @endif
        <button class="boton boton-principal" type="submit">Guardar</button>
    </form>
</div>
@endsection
