<form method="POST" action="{{ $ruta }}" data-confirmar="¿Confirma esta decisión?" style="margin-top:18px;">
    @csrf
    <div class="acciones">
        <button class="boton boton-ok" type="submit" name="decision" value="aprobada">Aprobar</button>
        <button class="boton boton-peligro" type="submit" name="decision" value="rechazada">Rechazar</button>
    </div>
</form>
