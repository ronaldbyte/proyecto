@extends('layouts.principal')
@section('titulo', 'Jefes de personal')
@section('contenido')
<div class="acciones" style="margin-bottom:16px;">
    <a class="boton boton-principal" href="{{ route('jefatura.personal.crear') }}">Registrar jefe de personal</a>
</div>
<div class="tarjeta">
    <table class="tabla">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Cargo</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($jefes as $jefe)
                <tr>
                    <td>{{ $jefe->usuario->codigo }}</td>
                    <td>{{ $jefe->nombre_completo }}</td>
                    <td>{{ $jefe->usuario->email }}</td>
                    <td>{{ $jefe->cargo }}</td>
                    <td>{{ $jefe->usuario->activo ? 'Activo' : 'Inactivo' }}</td>
                    <td class="acciones">
                        <a class="boton boton-suave" href="{{ route('jefatura.personal.editar', $jefe) }}">Editar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="vacio">No hay jefes de personal registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
