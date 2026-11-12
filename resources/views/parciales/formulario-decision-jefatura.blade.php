<form method="POST" action="{{ $ruta }}" style="margin-top:24px; padding:20px; border:1px solid var(--borde); border-radius:12px; background:#fbfcfe;">
    @csrf
    <h4 style="margin:0 0 14px; font-size:15px; color:var(--azul-marino);">Decisión final de Jefatura de Personal</h4>
    <div class="campo" style="margin-bottom:16px;">
        <label style="font-weight:700; color:var(--azul-marino); font-size:13px;">Observación o motivo institucional (opcional):</label>
        <textarea name="observacion" rows="2" placeholder="Agregue una observación para el docente (opcional en aprobación, recomendado en caso de rechazo)..." style="width:100%; border:1px solid var(--borde); border-radius:8px; padding:10px; font-family:inherit;"></textarea>
    </div>
    <div class="acciones">
        <button class="boton boton-ok" type="submit" name="decision" value="aprobada">Aprobar</button>
        <button class="boton boton-peligro" type="submit" name="decision" value="rechazada">Rechazar</button>
    </div>
</form>
