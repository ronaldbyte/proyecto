<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('titulo') — UNSXX</title>
    <link rel="stylesheet" href="{{ asset('css/institucional.css') }}">
    <style>
        body { background: #fff; }
        .membrete { display:flex; gap:16px; align-items:center; border-bottom:4px solid #c41e3a; padding-bottom:12px; margin-bottom:18px; }
        .membrete img { width:78px; height:78px; border-radius:50%; background:#000; }
        h1 { margin:0; font-size:20px; color:#0b2a5b; }
        @media print { .no-imprimir { display:none !important; } }
    </style>
</head>
<body>
<div class="pagina">
    <div class="membrete" style="display:flex; justify-content:space-between; align-items:center; border-bottom:4px solid #c41e3a; padding-bottom:12px; margin-bottom:18px;">
        <div style="display:flex; gap:16px; align-items:center;">
            <img src="{{ asset('imagenes/escudo.png') }}" alt="Escudo UNSXX" style="width:68px; height:68px; object-fit:contain;">
            <div>
                <h1 style="margin:0; font-size:18px; color:#0b2a5b; font-weight:900; font-family:'Arial Black', Arial, sans-serif;">UNIVERSIDAD NACIONAL "SIGLO XX"</h1>
                <p style="margin:2px 0 0 0; font-size:12px; font-weight:700; color:#334155;">DEPARTAMENTO DE PERSONAL</p>
                <small style="color:#64748b;">Gestión de licencias y vacaciones · Llallagua · Potosí · Bolivia</small>
            </div>
        </div>
        <img src="{{ asset('imagenes/logo-personal.jpg') }}" alt="Jefatura de Personal" style="height:50px; width:auto; object-fit:contain;">
    </div>
    <div class="no-imprimir acciones" style="margin-bottom:16px;">
        <button class="boton boton-principal" type="button" onclick="window.print()">Imprimir</button>
    </div>
    @yield('contenido')
</div>
</body>
</html>
