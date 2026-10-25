<nav class="menu">
    <a href="{{ route('panel') }}" class="{{ request()->routeIs('panel') ? 'activo' : '' }}">Inicio</a>
    <a href="{{ route('notificaciones.listar') }}" class="{{ request()->routeIs('notificaciones.*') ? 'activo' : '' }}">Notificaciones</a>

    @if(auth()->user()->puedeSolicitarLicencia())
        <div class="grupo">MI LICENCIA</div>
        <a href="{{ route('solicitudes.crear') }}" class="{{ request()->routeIs('solicitudes.crear') ? 'activo' : '' }}">Solicitar licencia</a>
        <a href="{{ route('solicitudes.mias') }}" class="{{ request()->routeIs('solicitudes.mias') ? 'activo' : '' }}">Mis solicitudes</a>
        <a href="{{ route('historial.propio') }}" class="{{ request()->routeIs('historial.propio') ? 'activo' : '' }}">Mi historial</a>
    @endif

    @if(auth()->user()->esDirector())
        <div class="grupo">DIRECCIÓN</div>
        <a href="{{ route('director.pendientes') }}" class="{{ request()->routeIs('director.pendientes') ? 'activo' : '' }}">Solicitudes pendientes</a>
        <a href="{{ route('director.historial') }}" class="{{ request()->routeIs('director.historial') ? 'activo' : '' }}">Historial de revisión</a>
    @endif

    @if(auth()->user()->esJefatura())
        <div class="grupo">JEFATURA DE PERSONAL</div>
        <a href="{{ route('jefatura.solicitudes.pendientes') }}" class="{{ request()->routeIs('jefatura.solicitudes.pendientes') ? 'activo' : '' }}">Decisión final</a>
        <a href="{{ route('jefatura.solicitudes.listar') }}" class="{{ request()->routeIs('jefatura.solicitudes.listar') ? 'activo' : '' }}">Todas las solicitudes</a>
        <a href="{{ route('jefatura.docentes.listar') }}" class="{{ request()->routeIs('jefatura.docentes.*') ? 'activo' : '' }}">Docentes</a>
        <a href="{{ route('jefatura.personal.listar') }}" class="{{ request()->routeIs('jefatura.personal.*') ? 'activo' : '' }}">Jefes de personal</a>
        <a href="{{ route('jefatura.roles') }}" class="{{ request()->routeIs('jefatura.roles') ? 'activo' : '' }}">Roles</a>
        <a href="{{ route('jefatura.avisos') }}" class="{{ request()->routeIs('jefatura.avisos*') ? 'activo' : '' }}">Avisos al celular</a>
        <a href="{{ route('jefatura.imprimir.solicitudes') }}" class="{{ request()->routeIs('jefatura.imprimir.*') ? 'activo' : '' }}">Imprimir solicitudes</a>
    @endif

    <div class="grupo">MI CUENTA</div>
    <a href="{{ route('contrasena.editar') }}" class="{{ request()->routeIs('contrasena.editar') ? 'activo' : '' }}">Cambiar contraseña</a>
</nav>
