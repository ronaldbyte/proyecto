<nav class="menu">
    <a href="{{ route('panel') }}" class="{{ request()->routeIs('panel') ? 'activo' : '' }}">
        <span>Inicio</span>
    </a>
    <a href="{{ route('notificaciones.listar') }}" class="{{ request()->routeIs('notificaciones.*') ? 'activo' : '' }}">
        <span>Notificaciones</span>
        @if(($cantidadNoLeidas ?? 0) > 0)
            <span class="insignia-menu">{{ $cantidadNoLeidas }}</span>
        @endif
    </a>

    @if(auth()->user()->puedeSolicitarLicencia())
        <div class="grupo">MI LICENCIA</div>
        <a href="{{ route('solicitudes.crear') }}" class="{{ request()->routeIs('solicitudes.crear') ? 'activo' : '' }}">
            <span>Solicitar licencia</span>
        </a>
        <a href="{{ route('solicitudes.mias') }}" class="{{ request()->routeIs('solicitudes.mias') ? 'activo' : '' }}">
            <span>Mis solicitudes</span>
        </a>
        <a href="{{ route('historial.propio') }}" class="{{ request()->routeIs('historial.propio') ? 'activo' : '' }}">
            <span>Mi historial</span>
        </a>
    @endif

    @if(auth()->user()->esDirector())
        <div class="grupo">DIRECCIÓN</div>
        <a href="{{ route('director.pendientes') }}" class="{{ request()->routeIs('director.pendientes') ? 'activo' : '' }}">
            <span>Solicitudes pendientes</span>
            @if(($pendientesDirectorCount ?? 0) > 0)
                <span class="insignia-menu">{{ $pendientesDirectorCount }}</span>
            @endif
        </a>
        <a href="{{ route('director.historial') }}" class="{{ request()->routeIs('director.historial') ? 'activo' : '' }}">
            <span>Historial de revisión</span>
        </a>
    @endif

    @if(auth()->user()->esJefatura())
        <div class="grupo">JEFATURA DE PERSONAL</div>
        <a href="{{ route('jefatura.solicitudes.pendientes') }}" class="{{ request()->routeIs('jefatura.solicitudes.pendientes') ? 'activo' : '' }}">
            <span>Decisión final</span>
            @if(($pendientesJefaturaCount ?? 0) > 0)
                <span class="insignia-menu">{{ $pendientesJefaturaCount }}</span>
            @endif
        </a>
        <a href="{{ route('jefatura.solicitudes.listar') }}" class="{{ request()->routeIs('jefatura.solicitudes.listar') ? 'activo' : '' }}">
            <span>Todas las solicitudes</span>
            @if(($todasPendientesJefaturaCount ?? 0) > 0)
                <span class="insignia-menu">{{ $todasPendientesJefaturaCount }}</span>
            @endif
        </a>
        <a href="{{ route('jefatura.docentes.listar') }}" class="{{ request()->routeIs('jefatura.docentes.*') ? 'activo' : '' }}">
            <span>Docentes</span>
        </a>
        <a href="{{ route('jefatura.personal.listar') }}" class="{{ request()->routeIs('jefatura.personal.*') ? 'activo' : '' }}">
            <span>Jefes de personal</span>
        </a>
        <a href="{{ route('jefatura.roles') }}" class="{{ request()->routeIs('jefatura.roles') ? 'activo' : '' }}">
            <span>Roles</span>
        </a>
        <a href="{{ route('jefatura.avisos') }}" class="{{ request()->routeIs('jefatura.avisos*') ? 'activo' : '' }}">
            <span>Avisos al celular</span>
        </a>
        <a href="{{ route('jefatura.imprimir.solicitudes') }}" target="_blank" class="{{ request()->routeIs('jefatura.imprimir.*') ? 'activo' : '' }}">
            <span>Imprimir solicitudes</span>
        </a>
        <a href="{{ route('jefatura.respaldo') }}" class="{{ request()->routeIs('jefatura.respaldo*') ? 'activo' : '' }}">
            <span>Copia de seguridad</span>
        </a>
    @endif

    <div class="grupo">MI CUENTA</div>
    <a href="{{ route('perfil.editar') }}" class="{{ request()->routeIs('perfil.*') ? 'activo' : '' }}">
        <span>Mi perfil y foto</span>
    </a>
    <a href="{{ route('contrasena.editar') }}" class="{{ request()->routeIs('contrasena.editar') ? 'activo' : '' }}">
        <span>Cambiar contraseña</span>
    </a>
</nav>
