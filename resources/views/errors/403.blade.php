<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso no autorizado — UNSXX</title>
    <link rel="stylesheet" href="{{ asset('css/institucional.css') }}">
</head>
<body>
<div class="pagina">
    <div class="tarjeta" style="max-width:560px;margin:80px auto;text-align:center;">
        <h2>No tiene autorización para esta sección</h2>
        <p>El sistema protege las funciones según el rol. Si cree que se trata de un error, consulte con Jefatura de Personal.</p>
        <a class="boton boton-principal" href="{{ url()->previous() }}">Volver</a>
        <a class="boton boton-suave" href="{{ route('panel') }}">Ir al panel</a>
    </div>
</div>
</body>
</html>
