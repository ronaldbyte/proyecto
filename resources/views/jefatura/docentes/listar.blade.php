@extends('layouts.principal')
@section('titulo', 'Docentes')
@section('contenido')
<form class="filtros" method="GET">
    <input name="buscar" value="{{ request('buscar') }}" placeholder="Código o nombre">
    <select name="carrera_id">
        @include('parciales.opciones-carreras', [
            'areasCatalogo' => $areas,
            'seleccionada' => request('carrera_id'),
            'textoVacio' => 'Todas las carreras',
        ])
    </select>
    <select name="area_id">
        <option value="">Todas las áreas</option>
        @foreach($areas as $area)
            <option value="{{ $area->id }}" @selected(request('area_id')==$area->id)>{{ $area->nombre }}</option>
        @endforeach
    </select>
    <select name="rol_id">
        <option value="">Todos los roles</option>
        @foreach($roles as $rol)
            <option value="{{ $rol->id }}" @selected(request('rol_id')==$rol->id)>{{ $rol->nombre }}</option>
        @endforeach
    </select>
    <select name="tipo_contrato">
        <option value="">Todos los contratos</option>
        @foreach($tiposContrato as $codigo => $nombre)
            <option value="{{ $codigo }}" @selected(request('tipo_contrato')==$codigo)>{{ $nombre }}</option>
        @endforeach
    </select>
    <label style="display:flex;align-items:center;gap:6px;font-size:13px;">
        <input type="checkbox" name="excedidos" value="1" @checked(request('excedidos')==='1')> Solo excedidos
    </label>
    <button class="boton boton-azul" type="submit">Buscar</button>
    <a class="boton boton-principal" href="{{ route('jefatura.docentes.crear') }}">Registrar docente</a>
</form>
<div class="tarjeta">
    <table class="tabla">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Carrera</th>
                <th>Área</th>
                <th>Rol</th>
                <th>Contrato</th>
                <th>Disponibles</th>
                <th>Excedidos</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($docentes as $docente)
                <tr>
                    <td>{{ $docente->usuario->codigo }}</td>
                    <td>{{ $docente->nombre_completo }}</td>
                    <td>{{ $docente->carrera->nombre }}</td>
                    <td>{{ $docente->area->nombre }}</td>
                    <td>{{ $docente->usuario->rol->nombre }}</td>
                    <td>{{ $docente->etiquetaContrato() }}</td>
                    <td>{{ $docente->dias_disponibles }}</td>
                    <td>
                        @if($docente->tieneDiasExcedidos())
                            <span class="estado estado-rechazada">{{ $docente->dias_excedidos }}</span>
                        @else
                            0
                        @endif
                    </td>
                    <td>{{ $docente->usuario->activo ? 'Activo' : 'Inactivo' }}</td>
                    <td class="acciones">
                        <a class="boton boton-suave" href="{{ route('jefatura.docentes.ver', $docente) }}">Ver</a>
                        <a class="boton boton-suave" href="{{ route('jefatura.docentes.editar', $docente) }}">Editar</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="paginacion">{{ $docentes->links() }}</div>
</div>
@endsection
