@extends('layouts.principal')
@section('titulo', 'Mi Perfil')
@section('subtitulo', 'Información de su cuenta y personalización de foto de perfil')
@section('contenido')
<div class="tarjeta" style="max-width: 640px; margin: 0 auto;">
    <div style="display: flex; align-items: center; gap: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--borde); margin-bottom: 20px;">
        <div style="position: relative; flex-shrink: 0;">
            @if($usuario->foto)
                <img id="vista-previa-foto" src="{{ $usuario->foto }}" alt="Foto de perfil actual" style="width: 84px; height: 84px; border-radius: 50%; object-fit: cover; border: 3px solid var(--azul-medio); box-shadow: 0 4px 10px rgba(0,0,0,.15); display: block;">
            @else
                <div id="silueta-defecto" class="avatar-silueta" style="width: 84px; height: 84px; border-radius: 50%; background: #e8eef8; color: var(--azul-marino); border: 3px solid #cbdcf5; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,.1);">
                    <svg width="42" height="42" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <img id="vista-previa-foto" src="" alt="Vista previa de foto" style="display: none; width: 84px; height: 84px; border-radius: 50%; object-fit: cover; border: 3px solid var(--azul-medio); box-shadow: 0 4px 10px rgba(0,0,0,.15);">
            @endif
        </div>
        <div>
            <h3 style="margin: 0 0 4px; font-size: 18px; color: var(--azul-marino);">{{ $usuario->nombreVisible() }}</h3>
            <span style="display: inline-block; background: #eef3fb; color: var(--azul-marino); font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 6px; margin-bottom: 4px;">
                {{ $usuario->rol->nombre }}
            </span>
            <div style="font-size: 12px; color: var(--gris);">Código: <strong>{{ $usuario->codigo }}</strong> · CI: <strong>{{ $usuario->ci }}</strong></div>
        </div>
    </div>

    <h4 style="margin: 0 0 12px; font-size: 15px; color: var(--azul-marino);">Cambiar Foto de Perfil</h4>
    <p style="font-size: 13px; color: var(--gris); margin-top: 0; margin-bottom: 14px;">
        Suba una imagen formal para identificar su perfil en el sistema. Formatos aceptados: JPG, PNG, WEBP (máx. 3 MB).
    </p>

    <form method="POST" action="{{ route('perfil.foto.actualizar') }}" enctype="multipart/form-data" style="margin-bottom: 16px;">
        @csrf
        <div class="campo">
            <label for="inp-foto">Seleccionar nueva foto</label>
            <input type="file" name="foto" id="inp-foto" accept="image/jpeg,image/png,image/webp" required onchange="mostrarVistaPrevia(this)">
        </div>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <button class="boton boton-principal" type="submit" id="btn-guardar-foto">Guardar foto de perfil</button>
        </div>
    </form>

    @if($usuario->foto)
        <form method="POST" action="{{ route('perfil.foto.eliminar') }}" data-confirmar="¿Está seguro de quitar su foto y volver a la silueta por defecto?" style="margin-top: 10px; padding-top: 10px; border-top: 1px dotted var(--borde);">
            @csrf
            <button class="boton boton-suave" type="submit" style="color: var(--rojo-unsxx);">
                ✕ Quitar foto y usar silueta por defecto
            </button>
        </form>
    @endif

    <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--borde);">
        <h4 style="margin: 0 0 12px; font-size: 15px; color: var(--azul-marino);">Datos de la Cuenta</h4>
        <div class="datos-fijos" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
            <div><small>Correo institucional</small><strong>{{ $usuario->email }}</strong></div>
            @if($usuario->docente)
                <div><small>Carrera</small><strong>{{ $usuario->docente->carrera->nombre ?? 'N/A' }}</strong></div>
                <div><small>Área</small><strong>{{ $usuario->docente->area->nombre ?? 'N/A' }}</strong></div>
                <div><small>Cargo</small><strong>{{ $usuario->docente->cargo ?? 'Docente' }}</strong></div>
                <div><small>Teléfono</small><strong>{{ $usuario->docente->telefono ?? 'No registrado' }}</strong></div>
            @endif
        </div>
        <div style="margin-top: 16px;">
            <a class="boton boton-suave" href="{{ route('contrasena.editar') }}">Cambiar mi contraseña →</a>
        </div>
    </div>
</div>

<script>
function mostrarVistaPrevia(input) {
    if (input.files && input.files[0]) {
        var lector = new FileReader();
        lector.onload = function (e) {
            var img = document.getElementById('vista-previa-foto');
            var silueta = document.getElementById('silueta-defecto');
            if (img) {
                img.src = e.target.result;
                img.style.display = 'block';
            }
            if (silueta) {
                silueta.style.display = 'none';
            }
        };
        lector.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
