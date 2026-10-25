@extends('layouts.principal')
@section('titulo', 'Mi historial')
@section('contenido')
@include('parciales.ficha-docente', ['docente' => $docente, 'solicitudes' => $solicitudes])
@endsection
