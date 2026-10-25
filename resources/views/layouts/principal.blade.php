<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Panel') — UNSXX</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/institucional.css') }}">
</head>
<body>
<div class="cuerpo-app">
    <div class="fondo-movil" id="fondo-movil"></div>
    <aside class="barra-lateral" id="barra-lateral">
        <div class="marca">
            <img src="{{ asset('imagenes/escudo.png') }}" alt="Escudo de la Universidad Nacional Siglo XX">
            <div>
                <strong>Universidad Nacional "Siglo XX"</strong>
                <span>Licencias y vacaciones</span>
            </div>
        </div>
        @include('parciales.menu')
        <div class="salir-caja">
            <form method="POST" action="{{ route('salir') }}">
                @csrf
                <button class="boton boton-peligro boton-bloque" type="submit">Cerrar sesión</button>
            </form>
        </div>
    </aside>
    <div class="contenido">
        <header class="cabecera">
            <div>
                <div class="cabecera-acciones">
                    <button class="boton boton-suave menu-movil" id="abrir-menu" type="button">Menú</button>
                    @unless(request()->routeIs('panel'))
                        <button
                            type="button"
                            class="boton boton-suave boton-volver"
                            id="boton-volver"
                            data-panel="{{ route('panel') }}"
                        >← Volver</button>
                    @endunless
                </div>
                <h2>@yield('titulo', 'Panel')</h2>
                <p>@yield('subtitulo', 'Universidad Nacional Siglo XX · Llallagua, Potosí')</p>
            </div>
            <div class="usuario-caja">
                <a
                    class="campana"
                    id="campana-notificaciones"
                    href="{{ route('notificaciones.listar') }}"
                    title="Notificaciones"
                    data-estado="{{ route('notificaciones.estado') }}"
                >
                    N
                    @if(($cantidadNoLeidas ?? 0) > 0)
                        <span class="punto">{{ $cantidadNoLeidas }}</span>
                    @endif
                </a>
                <div>
                    <strong>{{ auth()->user()->nombreVisible() }}</strong><br>
                    <small>{{ auth()->user()->codigo }} · {{ auth()->user()->rol->nombre }}</small>
                </div>
            </div>
        </header>
        <main class="pagina">
            @if(session('exito'))
                <div class="exito">{{ session('exito') }}</div>
            @endif
            @if(session('error'))
                <div class="errores">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="errores">
                    {{ $errors->first() }}
                </div>
            @endif
            @yield('contenido')
        </main>
    </div>
</div>
<script src="{{ asset('js/aplicacion.js') }}"></script>
</body>
</html>
