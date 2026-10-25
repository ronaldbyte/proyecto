@extends('layouts.principal')
@section('titulo', 'Roles del sistema')
@section('contenido')
<div class="tarjeta">
    <table class="tabla">
        <thead>
            <tr>
                <th>Rol</th>
                <th>Código</th>
                <th>Descripción</th>
                <th>Usuarios</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $rol)
                <tr>
                    <td>{{ $rol->nombre }}</td>
                    <td>{{ $rol->codigo }}</td>
                    <td>{{ $rol->descripcion }}</td>
                    <td>{{ $rol->usuarios_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
