<nav class="pestanas no-imprimir">
    <a href="{{ route('perfil.editar') }}" class="{{ request()->routeIs('perfil.*') ? 'activa' : '' }}">Mi perfil</a>
    <a href="{{ route('contrasena.editar') }}" class="{{ request()->routeIs('contrasena.*') ? 'activa' : '' }}">Cambiar contraseña</a>
    @if(auth()->user()->esJefatura())
        <a href="{{ route('jefatura.avisos') }}" class="{{ request()->routeIs('jefatura.avisos*') ? 'activa' : '' }}">Avisos al celular</a>
    @endif
</nav>

