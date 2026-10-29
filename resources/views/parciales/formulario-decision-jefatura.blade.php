<form method="POST" action="{{ $ruta }}" data-confirmar="¿Confirma registrar esta decisión final sobre la solicitud?" style="margin-top:20px; padding:18px; border:1px solid var(--borde); border-radius:12px; background:#fbfcfe;">
    @csrf
    <h4 style="margin:0 0 12px; font-size:15px; color:var(--azul-marino);">Decisión Final de Jefatura de Personal</h4>
    <div class="campo" style="margin-bottom:14px;">
        <label style="font-weight:700; color:var(--azul-marino); font-size:13px;">Observación o motivo institucional (opcional):</label>
        <textarea name="observacion" rows="2" placeholder="Agregue una observación para el docente (opcional en aprobación, recomendado en caso de rechazo)..." style="width:100%; border:1px solid var(--borde); border-radius:8px; padding:10px; font-family:inherit;"></textarea>
    </div>
    <div class="acciones" style="display:flex; gap:12px; flex-wrap:wrap;">
        <button class="boton boton-ok" type="submit" name="decision" value="aprobada" style="flex:1; padding:12px 18px; font-size:14px; font-weight:700;">
            ✓ Aprobar Solicitud
        </button>
        <button class="boton boton-peligro" type="submit" name="decision" value="rechazada" style="flex:1; padding:12px 18px; font-size:14px; font-weight:700;">
            ✕ Rechazar Solicitud
        </button>
    </div>
</form>
