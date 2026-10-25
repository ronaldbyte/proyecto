<form method="POST" action="{{ $ruta }}" data-confirmar="¿Confirma la decisión final?" style="margin-top:18px;">
    @csrf
    <div class="campo">
        <label>Decisión final</label>
        <select name="decision" required>
            <option value="aprobada">Aprobar</option>
            <option value="rechazada">Rechazar</option>
        </select>
    </div>
    <div class="campo">
        <label>Observación (opcional)</label>
        <textarea name="observacion"></textarea>
    </div>
    <button class="boton boton-principal" type="submit">Registrar decisión final</button>
</form>
