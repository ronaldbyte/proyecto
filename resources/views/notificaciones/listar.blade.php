@extends('layouts.principal')
@section('titulo', 'Notificaciones')
@section('contenido')
<div class="acciones" style="margin-bottom:16px;">
    <form method="POST" action="{{ route('notificaciones.todas') }}">
        @csrf
        <button class="boton boton-suave" type="submit">Marcar todas como leídas</button>
    </form>
</div>
<div class="tarjeta">
    @forelse($notificaciones as $notificacion)
        <div style="padding:12px 0;border-bottom:1px solid var(--borde); {{ $notificacion->leida ? '' : 'background:#f7f9fd;border-radius:8px;padding:12px;' }}">
            <strong>{{ $notificacion->titulo }}</strong>
            <span class="estado {{ $notificacion->leida ? 'estado-cancelada' : 'estado-jefatura' }}">{{ $notificacion->leida ? 'Leída' : 'No leída' }}</span>
            <p>{{ $notificacion->mensaje }}</p>
            <small>{{ $notificacion->created_at->format('d/m/Y H:i') }} · {{ $notificacion->tipo }}</small>
            @if(! $notificacion->leida)
                <form method="POST" action="{{ route('notificaciones.leer', $notificacion) }}" style="margin-top:8px;">
                    @csrf
                    <button class="boton boton-azul" type="submit">Marcar como leída</button>
                </form>
            @endif
        </div>
    @empty
        <p class="vacio">No tiene notificaciones.</p>
    @endforelse
    <div class="paginacion">{{ $notificaciones->links() }}</div>
</div>
@endsection
