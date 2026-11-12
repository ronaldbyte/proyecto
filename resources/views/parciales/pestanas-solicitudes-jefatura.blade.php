<nav class="pestanas no-imprimir">
    <a href="{{ route('jefatura.solicitudes.pendientes') }}" class="{{ request()->routeIs('jefatura.solicitudes.pendientes') ? 'activa' : '' }}">
        Pendientes
        @if(($pendientesJefaturaCount ?? 0) > 0)
            <span class="insignia-menu">{{ $pendientesJefaturaCount }}</span>
        @endif
    </a>
    <a href="{{ route('jefatura.solicitudes.listar') }}" class="{{ request()->routeIs('jefatura.solicitudes.listar') ? 'activa' : '' }}">Historial</a>
</nav>

