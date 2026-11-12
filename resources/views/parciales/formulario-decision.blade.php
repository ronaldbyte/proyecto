<form method="POST" action="{{ $ruta }}" style="margin-top:24px;">
    @csrf
    <div class="acciones">
        <button class="boton boton-ok" type="submit" name="decision" value="aprobada">Aprobar</button>
        <button class="boton boton-peligro" type="submit" name="decision" value="rechazada">Rechazar</button>
    </div>
</form>
