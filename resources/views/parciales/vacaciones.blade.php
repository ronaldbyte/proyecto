@php
    $docente = $docente ?? null;
    $diasPendientesDocente = $docente ? (float) $docente->dias_pendientes : 0.0;
@endphp
@if($docente)
    @if(! $docente->gozaVacaciones())
        <div class="alerta">No goza de vacaciones.</div>
    @else
<div class="rejilla rejilla-4" style="gap: 12px; margin-bottom: 16px;">
    <div class="tarjeta metrica" style="margin: 0 !important;">
        <span>Días asignados</span>
        <strong>{{ $docente->dias_asignados }}</strong>
    </div>
    <div class="tarjeta metrica" style="margin: 0 !important;">
        <span>Licencias</span>
        <strong id="lbl-dias-licencias">{{ $docente->dias_utilizados }}</strong>
    </div>
    <div class="tarjeta metrica" style="margin: 0 !important;">
        <span>Días disponibles</span>
        <strong id="lbl-dias-disponibles">{{ $docente->dias_disponibles }}</strong>
    </div>
    <div class="tarjeta metrica" id="caja-excedidos" style="margin: 0 !important;">
        <span>Días excedidos</span>
        <strong id="lbl-dias-excedidos">{{ $docente->dias_excedidos }}</strong>
    </div>
</div>

@if(Auth::user()->esJefatura())
<div class="no-imprimir" style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--borde);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px; flex-wrap:wrap; gap:8px;">
        <h3 style="margin: 0; font-size: 16px;">Configuración para Boleta de Impresión</h3>
        <span id="alerta-guardado-boleta" style="font-size:12px; color:#16a34a; font-weight:600; display:none;"></span>
    </div>
    <div class="rejilla rejilla-3" style="gap: 12px; margin-bottom: 12px;">
        <div class="tarjeta" style="margin: 0 !important; display:flex; flex-direction:column; justify-content:space-between; padding:8px 12px; height:64px; max-height:64px; box-sizing:border-box;">
            <span style="font-size:12px; color:var(--gris); font-weight:500; height:16px; line-height:16px; margin:0;">Gestión</span>
            <input type="text" id="inp-gestion" value="{{ (int) date('Y') - 1 }}-{{ date('Y') }}" onfocus="this.select()" style="padding:2px 8px; border:1px solid var(--borde); border-radius:6px; font-size:13px; text-align:center; height:30px; min-height:30px; max-height:30px; box-sizing:border-box; width:100%;" oninput="calcularVacaciones()" onchange="calcularVacaciones()">
        </div>
        <div class="tarjeta" style="margin: 0 !important; display:flex; flex-direction:column; justify-content:space-between; padding:8px 12px; height:64px; max-height:64px; box-sizing:border-box;">
            <span style="font-size:12px; color:var(--gris); font-weight:500; height:16px; line-height:16px; margin:0;">A partir de</span>
            <input type="date" id="inp-fecha-inicio" value="{{ (int) date('Y') + 1 }}-01-01" style="padding:2px 8px; border:1px solid var(--borde); border-radius:6px; font-size:13px; text-align:center; height:30px; min-height:30px; max-height:30px; box-sizing:border-box; width:100%;" oninput="calcularVacaciones()" onchange="calcularVacaciones()">
        </div>
        <div class="tarjeta" style="margin: 0 !important; display:flex; flex-direction:column; justify-content:space-between; padding:8px 12px; height:64px; max-height:64px; box-sizing:border-box;">
            <span style="font-size:12px; color:var(--gris); font-weight:500; height:16px; line-height:16px; margin:0;">Días Acumulados</span>
            <input type="number" step="0.5" id="inp-acumulados" min="0" max="100" value="0" placeholder="0" onfocus="this.select()" oninput="limpiarCero(this); calcularVacaciones()" onchange="calcularVacaciones()" style="padding:2px 8px; border:1px solid var(--borde); border-radius:6px; font-size:13px; text-align:center; font-weight:bold; color:var(--azul-marino); height:30px; min-height:30px; max-height:30px; box-sizing:border-box; width:100%;">
        </div>
    </div>
    <div class="rejilla rejilla-4" style="gap: 12px; margin-bottom: 12px;">
        <div class="tarjeta" style="margin: 0 !important; display:flex; flex-direction:column; justify-content:space-between; padding:8px 12px; height:64px; max-height:64px; box-sizing:border-box;">
            <span style="font-size:12px; color:var(--gris); font-weight:500; height:16px; line-height:16px; margin:0;">Licencias pasadas</span>
            <input type="number" step="0.5" id="inp-licencias-pasadas" min="0" max="100" value="0" placeholder="0" onfocus="this.select()" oninput="limpiarCero(this); calcularVacaciones()" onchange="calcularVacaciones()" style="padding:2px 8px; border:1px solid var(--borde); border-radius:6px; font-size:13px; text-align:center; font-weight:bold; color:var(--azul-marino); height:30px; min-height:30px; max-height:30px; box-sizing:border-box; width:100%;">
        </div>
        <div class="tarjeta" style="margin: 0 !important; display:flex; flex-direction:column; justify-content:space-between; padding:8px 12px; height:64px; max-height:64px; box-sizing:border-box;">
            <span style="font-size:12px; color:var(--gris); font-weight:500; height:16px; line-height:16px; margin:0;">Días pendientes</span>
            <input type="number" step="0.5" id="inp-pendientes" min="0" max="100" value="{{ $diasPendientesDocente }}" placeholder="0" onfocus="this.select()" oninput="limpiarCero(this); calcularVacaciones()" onchange="calcularVacaciones()" style="padding:2px 8px; border:1px solid var(--borde); border-radius:6px; font-size:13px; text-align:center; font-weight:bold; color:var(--azul-marino); height:30px; min-height:30px; max-height:30px; box-sizing:border-box; width:100%;" title="Días pendientes en trámite o fuera del cálculo automático">
        </div>
        <div class="tarjeta" style="margin: 0 !important; display:flex; flex-direction:column; justify-content:space-between; padding:8px 12px; height:64px; max-height:64px; box-sizing:border-box;">
            <span style="font-size:12px; color:var(--gris); font-weight:500; height:16px; line-height:16px; margin:0;">Descanso Pedagógico</span>
            <input type="number" step="0.5" id="inp-descanso" min="0" max="60" value="5" placeholder="0" onfocus="this.select()" oninput="limpiarCero(this); calcularVacaciones()" onchange="calcularVacaciones()" style="padding:2px 8px; border:1px solid var(--borde); border-radius:6px; font-size:13px; text-align:center; font-weight:bold; color:var(--azul-marino); height:30px; min-height:30px; max-height:30px; box-sizing:border-box; width:100%;">
        </div>
        <div class="tarjeta" style="margin: 0 !important; display:flex; flex-direction:column; justify-content:space-between; padding:8px 12px; height:64px; max-height:64px; box-sizing:border-box;">
            <span style="font-size:12px; color:var(--gris); font-weight:500; height:16px; line-height:16px; margin:0;">Receso Fin de Año</span>
            <input type="number" step="0.5" id="inp-receso" min="0" max="60" value="3" placeholder="0" onfocus="this.select()" oninput="limpiarCero(this); calcularVacaciones()" onchange="calcularVacaciones()" style="padding:2px 8px; border:1px solid var(--borde); border-radius:6px; font-size:13px; text-align:center; font-weight:bold; color:var(--azul-marino); height:30px; min-height:30px; max-height:30px; box-sizing:border-box; width:100%;">
        </div>
    </div>
    <div style="margin-top: 16px; display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="boton boton-secundario" type="button" id="btn-restaurar-boleta" onclick="recuperarConfiguracionBoleta(true)" style="display:inline-flex; align-items:center; gap:6px;" title="Recupera las modificaciones anteriores guardadas para este docente">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Restaurar valores anteriores
        </button>
        <button class="boton boton-azul" type="button" id="btn-descargar-img" onclick="descargarImagenBoleta()" style="display:inline-flex; align-items:center; gap:6px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Descargar en PDF
        </button>
        <button class="boton boton-principal" type="button" onclick="imprimirBoleta()" style="display:inline-flex; align-items:center; gap:6px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Imprimir Boleta de Vacaciones
        </button>
    </div>
</div>

<div id="boleta-impresion" style="display: none;">
    @for ($i = 0; $i < 3; $i++)
    <div class="copia-boleta">
        <!-- Encabezado Institucional Centrado -->
        <div class="boleta-encabezado" style="display: grid; grid-template-columns: 60px 1fr 110px; align-items: center; border-bottom: 1.8px solid #000; padding-bottom: 5px; margin-bottom: 6px;">
            <!-- Izquierda: Escudo UNSXX -->
            <div style="text-align: left;">
                <img src="{{ asset('imagenes/escudo.png') }}" style="height: 50px; width: auto; object-fit: contain;" alt="Escudo UNSXX">
            </div>
            
            <!-- Centro: Título Institucional y Recuadro -->
            <div style="text-align: center;">
                <div style="font-size: 14px; font-weight: 900; letter-spacing: 0.5px; color: #000; font-family: 'Arial Black', Arial, Helvetica, sans-serif; line-height: 1.15;">
                    UNIVERSIDAD NACIONAL "SIGLO XX"
                </div>
                <div style="font-size: 11px; font-weight: 700; color: #1e293b; letter-spacing: 0.8px; line-height: 1.2; margin-top: 2px;">
                    DEPARTAMENTO DE PERSONAL
                </div>
                <div style="margin-top: 3px;">
                    <div style="display: inline-block; border: 1.5px solid #000; padding: 2px 14px; font-size: 10px; font-weight: 800; letter-spacing: 0.6px; text-transform: uppercase; background: #fff; line-height: 1.2;">
                        FORMULARIO DE VACACIÓN
                    </div>
                </div>
            </div>

            <!-- Derecha: Logo de Personal más grande y ancho -->
            <div style="text-align: right;">
                <img src="{{ asset('imagenes/logo-personal.jpg') }}" style="height: 52px; width: auto; max-width: 105px; object-fit: contain;" alt="Jefatura de Personal">
            </div>
        </div>

        <!-- Cuerpo: 2 Columnas aprovechando el ancho de la hoja -->
        <div style="display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 12px; margin-bottom: 4px;">
            <!-- Columna Izquierda: Datos del Docente + Texto de Fechas -->
            <div style="display: flex; flex-direction: column; justify-content: space-between;">
                <table style="width: 100%; border-collapse: collapse; font-size: 9.5px; line-height: 1.35;">
                    <tr>
                        <td style="font-weight: bold; width: 28%; border-bottom: 1px dotted #888; padding: 2px 3px;">Docente:</td>
                        <td style="border-bottom: 1px dotted #888; padding: 2px 3px; font-weight: bold;" colspan="3">{{ $docente->nombre_completo }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; border-bottom: 1px dotted #888; padding: 2px 3px;">Cargo:</td>
                        <td style="border-bottom: 1px dotted #888; padding: 2px 3px;">{{ $docente->cargo ?? 'Docente' }}</td>
                        <td style="font-weight: bold; width: 16%; border-bottom: 1px dotted #888; padding: 2px 3px;">Gestión:</td>
                        <td style="border-bottom: 1px dotted #888; padding: 2px 3px; font-weight: bold;" class="boleta-gestion">{{ (int) date('Y') - 1 }}-{{ date('Y') }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; border-bottom: 1px dotted #888; padding: 2px 3px;">Carrera / Área:</td>
                        <td style="border-bottom: 1px dotted #888; padding: 2px 3px;" colspan="3">{{ $docente->carrera->nombre ?? 'N/A' }} / {{ $docente->area->nombre ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; border-bottom: 1px dotted #888; padding: 2px 3px;">F. Ingreso:</td>
                        <td style="border-bottom: 1px dotted #888; padding: 2px 3px;">{{ $docente->fecha_ingreso ? $docente->fecha_ingreso->format('d/m/Y') : 'No registrada' }}</td>
                        <td style="font-weight: bold; border-bottom: 1px dotted #888; padding: 2px 3px;">Antigüedad:</td>
                        <td style="border-bottom: 1px dotted #888; padding: 2px 3px; font-weight: bold;">{{ $docente->antiguedad_anios }} años</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; border-bottom: 1px dotted #888; padding: 2px 3px;">F. Inicio:</td>
                        <td style="border-bottom: 1px dotted #888; padding: 2px 3px; font-weight: bold; color: #0b2a5b;" class="boleta-fecha-inicio">--/--/----</td>
                        <td style="font-weight: bold; border-bottom: 1px dotted #888; padding: 2px 3px;">F. Conclusión:</td>
                        <td style="border-bottom: 1px dotted #888; padding: 2px 3px; font-weight: bold; color: #0b2a5b;" class="boleta-fecha-conclusion">--/--/----</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; border-bottom: 1px dotted #888; padding: 2px 3px;">F. Retorno:</td>
                        <td style="border-bottom: 1px dotted #888; padding: 2px 3px; font-weight: bold; color: #0b2a5b;" colspan="3">
                            <span class="boleta-fecha-retorno">--/--/----</span>
                            <span style="font-size: 8.5px; font-weight: normal; color: #334155;">(<span class="boleta-hora-retorno">08:00 a.m.</span> &bull; <span class="boleta-jornada">Jornada Completa</span>)</span>
                        </td>
                    </tr>
                </table>

                <div class="parrafo-vacaciones" id="parrafo-vacaciones" style="font-size: 9px; text-align: justify; line-height: 1.35; margin-top: 5px; padding: 4px 6px; border: 1px solid #cbd5e1; background-color: #f8fafc; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                    El/la docente hará uso de sus descansos y vacaciones por un total de <strong><span class="boleta-total-texto">0 días</span></strong> (<span class="boleta-total">0</span> d) <strong>a partir del <span class="boleta-fecha-inicio">--/--/----</span></strong> hasta su <strong>conclusión el <span class="boleta-fecha-conclusion">--/--/----</span></strong>, debiendo retornar a sus funciones el <strong><span class="boleta-fecha-retorno">--/--/----</span> a horas <span class="boleta-hora-retorno">08:00 a.m.</span></strong> en modalidad de <strong><span class="boleta-jornada">Jornada Completa</span></strong>.
                </div>
                <div class="parrafo-no-vacaciones" id="parrafo-no-vacaciones" style="font-size: 9px; text-align: center; line-height: 1.3; color: #b91c1c; font-weight: bold; display: none; margin-top: 5px; padding: 4px 6px; border: 1px solid #fca5a5; background-color: #fef2f2; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                    EL DOCENTE NO GOZA DE VACACIONES POR TENER SALDO EN CONTRA (DEUDOR) O SALDO CERO.
                </div>
            </div>

            <!-- Columna Derecha: Detalle de Días -->
            <div>
                <table style="width: 100%; border-collapse: collapse; font-size: 9px; line-height: 1.25;">
                    <thead>
                        <tr>
                            <th colspan="2" style="text-align: center; font-size: 9.5px; border: 1px solid #000; padding: 2.5px; background-color: #f1f5f9; -webkit-print-color-adjust: exact; print-color-adjust: exact; font-weight: bold;">
                                DETALLE DE DÍAS
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 1px solid #000; padding: 2px 4px;">Días según antigüedad ({{ $docente->antiguedad_anios }} a):</td>
                            <td style="border: 1px solid #000; padding: 2px 4px; text-align: right; font-weight: bold; width: 48px;"><span class="boleta-dias-asignados">{{ $docente->dias_asignados }}</span> d</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 2px 4px;">Días acumulados anteriores:</td>
                            <td style="border: 1px solid #000; padding: 2px 4px; text-align: right;"><span class="boleta-acumulados">0</span> d</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 2px 4px;">Licencias a la fecha:</td>
                            <td style="border: 1px solid #000; padding: 2px 4px; text-align: right;"><span class="boleta-utilizados">{{ $docente->dias_utilizados }}</span> d</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 2px 4px;">Licencias pasadas:</td>
                            <td style="border: 1px solid #000; padding: 2px 4px; text-align: right;"><span class="boleta-licencias-pasadas">0</span> d</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 2px 4px;">Días pendientes:</td>
                            <td style="border: 1px solid #000; padding: 2px 4px; text-align: right; font-weight: bold; color: #0b2a5b;"><span class="boleta-pendientes">0</span> d</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 2px 4px;">Descanso Pedag. / Receso:</td>
                            <td style="border: 1px solid #000; padding: 2px 4px; text-align: right;"><span class="boleta-descanso">5</span> / <span class="boleta-receso">3</span></td>
                        </tr>
                        <tr style="background-color: #f1f5f9; -webkit-print-color-adjust: exact; print-color-adjust: exact; font-weight: bold;">
                            <td style="border: 1px solid #000; padding: 2.5px 4px;">TOTAL DÍAS DE AUSENCIA:</td>
                            <td style="border: 1px solid #000; padding: 2.5px 4px; text-align: right; font-size: 10px;"><span class="boleta-total">0</span> d</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Firmas con suficiente espacio para rúbrica y sello -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-top: 18px; text-align: center;">
            <div>
                <div style="border-top: 1.2px solid #000; padding-top: 3px; font-size: 9px; font-weight: bold;">Interesado(a)</div>
                <div style="font-size: 8px; color: #475569;">Docente</div>
            </div>
            <div>
                <div style="border-top: 1.2px solid #000; padding-top: 3px; font-size: 9px; font-weight: bold;">Vto. Bno. Director(a)</div>
                <div style="font-size: 8px; color: #475569;">Área / Carrera</div>
            </div>
            <div>
                <div style="border-top: 1.2px solid #000; padding-top: 3px; font-size: 9px; font-weight: bold;">Jefe de Personal</div>
                <div style="font-size: 8px; color: #475569;">U.N.S.X.X.</div>
            </div>
        </div>

        <!-- Pie de Boleta con Fecha de Impresión -->
        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 8px; color: #475569; margin-top: 6px; padding-top: 2px;">
            <span>Llallagua · Potosí · Bolivia &nbsp;·&nbsp; Fecha de impresión: <strong style="color: #0f172a; letter-spacing: 0.3px;" class="boleta-fecha-impresion">{{ now()->format('d/m/Y H:i') }}</strong></span>
        </div>
    </div>
    @endfor
</div>

<style>
    @media print {
        @page {
            size: letter portrait;
            margin: 5mm 8mm 5mm 8mm;
        }
        /* Ocultar elementos de la vista normal para que solo salga la boleta */
        body * {
            visibility: hidden;
        }
        /* Mostrar solamente el div boleta-impresion y sus hijos */
        #boleta-impresion, #boleta-impresion * {
            visibility: visible;
        }
        #boleta-impresion {
            display: flex !important;
            flex-direction: column;
            justify-content: space-between;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            min-height: 265mm;
            max-height: 269mm;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background: #fff;
            color: #000;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .copia-boleta {
            page-break-inside: avoid;
            break-inside: avoid;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 32.5%;
            padding: 6px 4px 10px 4px;
            border-bottom: 1.5px dashed #444;
        }
        /* Eliminar el borde punteado en la ultima copia para que no salga al final */
        .copia-boleta:last-child {
            border-bottom: none !important;
            padding-bottom: 0 !important;
        }
    }
</style>

<script>
    const diasAsignadosOriginal = {{ $docente->dias_asignados }};
    const diasUtilizadosOriginal = {{ $docente->dias_utilizados }};
    const diasPendientesDocenteOriginal = {{ $diasPendientesDocente }};
    
    function limpiarCero(input) {
        if (!input) return;
        if (input.value.length > 1 && input.value.startsWith('0') && !input.value.startsWith('0.')) {
            input.value = parseFloat(input.value) || 0;
        }
    }
    
    function calcularPascua(year) {
        let a = year % 19;
        let b = Math.floor(year / 100);
        let c = year % 100;
        let d = Math.floor(b / 4);
        let e = b % 4;
        let f = Math.floor((b + 8) / 25);
        let g = Math.floor((b - f + 1) / 3);
        let h = (19 * a + b - d - g + 15) % 30;
        let i = Math.floor(c / 4);
        let k = c % 4;
        let l = (32 + 2 * e + 2 * i - h - k) % 7;
        let m = Math.floor((a + 11 * h + 22 * l) / 451);
        let mes = Math.floor((h + l - 7 * m + 114) / 31);
        let dia = ((h + l - 7 * m + 114) % 31) + 1;
        return new Date(year, mes - 1, dia);
    }

    function claveFecha(fecha) {
        let y = fecha.getFullYear();
        let m = String(fecha.getMonth() + 1).padStart(2, '0');
        let d = String(fecha.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    const cacheFeriadosBolivia = {};
    function obtenerFeriadosBolivia(year) {
        if (cacheFeriadosBolivia[year]) {
            return cacheFeriadosBolivia[year];
        }

        let feriados = {};
        // Feriados fijos nacionales de Bolivia + Aniversario Cívico de Potosí (10 de Noviembre)
        let fijos = [
            '01-01', // Año Nuevo
            '01-22', // Día del Estado Plurinacional
            '05-01', // Día del Trabajo
            '06-21', // Año Nuevo Andino (Willkakuti)
            '08-06', // Día de la Independencia de Bolivia
            '11-02', // Todos los Santos
            '11-10', // Aniversario Cívico de Potosí
            '12-25'  // Navidad
        ];

        fijos.forEach(function (md) {
            let [m, d] = md.split('-').map(Number);
            let f = new Date(year, m - 1, d);
            feriados[claveFecha(f)] = true;

            // D.S. 2750: Si el feriado cae en domingo, se traslada al día lunes
            if (f.getDay() === 0) {
                let lunesTraslado = new Date(year, m - 1, d + 1);
                feriados[claveFecha(lunesTraslado)] = true;
            }
        });

        // Feriados móviles religiosos (según Pascua)
        let pascua = calcularPascua(year);

        // Carnaval: Lunes y Martes (-48 y -47 días)
        let lunesCarnaval = new Date(pascua.getFullYear(), pascua.getMonth(), pascua.getDate() - 48);
        let martesCarnaval = new Date(pascua.getFullYear(), pascua.getMonth(), pascua.getDate() - 47);
        feriados[claveFecha(lunesCarnaval)] = true;
        feriados[claveFecha(martesCarnaval)] = true;

        // Viernes Santo (-2 días)
        let viernesSanto = new Date(pascua.getFullYear(), pascua.getMonth(), pascua.getDate() - 2);
        feriados[claveFecha(viernesSanto)] = true;

        // Corpus Christi (+60 días)
        let corpusChristi = new Date(pascua.getFullYear(), pascua.getMonth(), pascua.getDate() + 60);
        feriados[claveFecha(corpusChristi)] = true;

        cacheFeriadosBolivia[year] = feriados;
        return feriados;
    }

    function esDiaHabilBolivia(fecha) {
        let dia = fecha.getDay();
        // 0 = Domingo, 6 = Sábado
        if (dia === 0 || dia === 6) {
            return false;
        }
        let feriados = obtenerFeriadosBolivia(fecha.getFullYear());
        if (feriados[claveFecha(fecha)]) {
            return false;
        }
        return true;
    }

    const storageKeyBoleta = 'boleta_config_docente_{{ $docente->id }}';

    function guardarConfiguracionBoleta() {
        try {
            let config = {
                gestion: document.getElementById('inp-gestion') ? document.getElementById('inp-gestion').value : '',
                fechaInicio: document.getElementById('inp-fecha-inicio') ? document.getElementById('inp-fecha-inicio').value : '',
                acumulados: document.getElementById('inp-acumulados') ? document.getElementById('inp-acumulados').value : '0',
                licenciasPasadas: document.getElementById('inp-licencias-pasadas') ? document.getElementById('inp-licencias-pasadas').value : '0',
                pendientes: document.getElementById('inp-pendientes') ? document.getElementById('inp-pendientes').value : '0',
                descanso: document.getElementById('inp-descanso') ? document.getElementById('inp-descanso').value : '5',
                receso: document.getElementById('inp-receso') ? document.getElementById('inp-receso').value : '3'
            };
            localStorage.setItem(storageKeyBoleta, JSON.stringify(config));
        } catch (e) {
            console.error('Error guardando configuración:', e);
        }
    }

    function recuperarConfiguracionBoleta(notificar = true) {
        try {
            let guardado = localStorage.getItem(storageKeyBoleta);
            if (!guardado) {
                if (notificar) {
                    alert('No hay modificaciones o configuraciones anteriores guardadas para este docente.');
                }
                return false;
            }
            let config = JSON.parse(guardado);
            if (config.gestion !== undefined && document.getElementById('inp-gestion')) {
                document.getElementById('inp-gestion').value = config.gestion;
            }
            if (config.fechaInicio !== undefined && document.getElementById('inp-fecha-inicio')) {
                document.getElementById('inp-fecha-inicio').value = config.fechaInicio;
            }
            if (config.acumulados !== undefined && document.getElementById('inp-acumulados')) {
                document.getElementById('inp-acumulados').value = config.acumulados;
            }
            if (config.licenciasPasadas !== undefined && document.getElementById('inp-licencias-pasadas')) {
                document.getElementById('inp-licencias-pasadas').value = config.licenciasPasadas;
            }
            if (config.pendientes !== undefined && document.getElementById('inp-pendientes')) {
                document.getElementById('inp-pendientes').value = config.pendientes;
            }
            if (config.descanso !== undefined && document.getElementById('inp-descanso')) {
                document.getElementById('inp-descanso').value = config.descanso;
            }
            if (config.receso !== undefined && document.getElementById('inp-receso')) {
                document.getElementById('inp-receso').value = config.receso;
            }
            calcularVacaciones(false);
            if (notificar) {
                let aviso = document.getElementById('alerta-guardado-boleta');
                if (aviso) {
                    aviso.textContent = 'Valores anteriores recuperados correctamente';
                    aviso.style.color = '#16a34a';
                    aviso.style.display = 'inline-block';
                    setTimeout(function() { aviso.style.display = 'none'; }, 3500);
                }
            }
            return true;
        } catch (e) {
            console.error('Error recuperando configuración:', e);
            return false;
        }
    }

    function calcularRangoVacacionesBolivia(fechaInicioObj, dias) {
        if (!fechaInicioObj || dias <= 0) {
            return {
                inicio: fechaInicioObj,
                conclusion: null,
                retorno: null,
                horaRetorno: '08:00 a.m.',
                etiquetaJornada: 'Jornada Completa'
            };
        }

        let cursor = new Date(fechaInicioObj.getTime());
        let diasEnteros = Math.floor(dias);
        let tieneMedioDia = Math.round((dias - diasEnteros) * 10) / 10 >= 0.5;
        let totalDiasHabiles = tieneMedioDia ? (diasEnteros + 1) : diasEnteros;

        if (totalDiasHabiles <= 0) {
            return {
                inicio: fechaInicioObj,
                conclusion: fechaInicioObj,
                retorno: fechaInicioObj,
                horaRetorno: '08:00 a.m.',
                etiquetaJornada: 'Jornada Completa'
            };
        }

        // Consumir únicamente los días hábiles laborales de vacación a partir de la fecha seleccionada
        let diasConsumidos = 0;
        let fechaConclusion = new Date(cursor.getTime());

        while (diasConsumidos < totalDiasHabiles) {
            if (esDiaHabilBolivia(cursor)) {
                diasConsumidos++;
                fechaConclusion = new Date(cursor.getTime());
                if (diasConsumidos === totalDiasHabiles) {
                    break;
                }
            }
            cursor.setDate(cursor.getDate() + 1);
        }

        if (tieneMedioDia) {
            // Si termina en medio día (0.5): retorno automático es ESA MISMA FECHA DE CONCLUSIÓN en la tarde (14:00 p.m.)
            return {
                inicio: fechaInicioObj,
                conclusion: fechaConclusion,
                retorno: new Date(fechaConclusion.getTime()),
                horaRetorno: '14:00 p.m.',
                etiquetaJornada: 'Media Jornada'
            };
        } else {
            // Si termina en día entero: retorno automático es al DÍA HÁBIL SIGUIENTE en la mañana (08:00 a.m.)
            cursor = new Date(fechaConclusion.getTime());
            cursor.setDate(cursor.getDate() + 1);
            while (!esDiaHabilBolivia(cursor)) {
                cursor.setDate(cursor.getDate() + 1);
            }
            return {
                inicio: fechaInicioObj,
                conclusion: fechaConclusion,
                retorno: new Date(cursor.getTime()),
                horaRetorno: '08:00 a.m.',
                etiquetaJornada: 'Jornada Completa'
            };
        }
    }

    function formatearFecha(fecha) {
        if (!fecha || !(fecha instanceof Date) || isNaN(fecha.getTime())) return '--/--/----';
        let d = fecha.getDate().toString().padStart(2, '0');
        let m = (fecha.getMonth() + 1).toString().padStart(2, '0');
        let y = fecha.getFullYear();
        return `${d}/${m}/${y}`;
    }

    function formatearDiasTexto(dias) {
        dias = Math.round((parseFloat(dias) || 0) * 10) / 10;
        if (dias <= 0) return '0 días';
        let enteros = Math.floor(dias);
        let tieneMedio = Math.abs(dias - enteros - 0.5) < 0.05;
        
        if (enteros === 0 && tieneMedio) {
            return 'medio día';
        }
        if (enteros === 1 && !tieneMedio) {
            return '1 día';
        }
        if (enteros === 1 && tieneMedio) {
            return '1 día y medio';
        }
        if (!tieneMedio) {
            return enteros + ' días';
        }
        return enteros + ' días y medio';
    }

    function actualizarClases(clase, valor) {
        document.querySelectorAll('.' + clase).forEach(el => el.textContent = valor);
    }

    function calcularVacaciones(autoGuardar = true) {
        let gestion = document.getElementById('inp-gestion') ? document.getElementById('inp-gestion').value : '';
        let acumulados = document.getElementById('inp-acumulados') ? (parseFloat(document.getElementById('inp-acumulados').value) || 0) : 0;
        let licenciasPasadas = document.getElementById('inp-licencias-pasadas') ? (parseFloat(document.getElementById('inp-licencias-pasadas').value) || 0) : 0;
        if (licenciasPasadas < 0) licenciasPasadas = 0;
        if (licenciasPasadas > 100) licenciasPasadas = 100;

        let pendientes = document.getElementById('inp-pendientes') ? (parseFloat(document.getElementById('inp-pendientes').value) || 0) : 0;
        if (pendientes < 0) pendientes = 0;

        let utilizados = diasUtilizadosOriginal;
        let descanso = document.getElementById('inp-descanso') ? (parseFloat(document.getElementById('inp-descanso').value) || 0) : 0;
        if (descanso < 0) descanso = 0;
        let receso = document.getElementById('inp-receso') ? (parseFloat(document.getElementById('inp-receso').value) || 0) : 0;
        if (receso < 0) receso = 0;
        let fechaInicioStr = document.getElementById('inp-fecha-inicio') ? document.getElementById('inp-fecha-inicio').value : '';
        
        // Actualizar UI general de datos impresos en todas las copias
        actualizarClases('boleta-gestion', gestion);
        actualizarClases('boleta-acumulados', acumulados);
        actualizarClases('boleta-utilizados', utilizados);
        actualizarClases('boleta-licencias-pasadas', licenciasPasadas);
        actualizarClases('boleta-pendientes', pendientes);
        actualizarClases('boleta-descanso', descanso);
        actualizarClases('boleta-receso', receso);

        // Convertir y actualizar fecha de inicio (A partir de) de inmediato en todas las copias
        let fechaInicioObj = null;
        if (fechaInicioStr) {
            let partes = fechaInicioStr.split('-');
            if (partes.length === 3) {
                let y = parseInt(partes[0], 10);
                let m = parseInt(partes[1], 10);
                let d = parseInt(partes[2], 10);
                if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {
                    fechaInicioObj = new Date(y, m - 1, d);
                }
            }
        }
        actualizarClases('boleta-fecha-inicio', formatearFecha(fechaInicioObj));
        
        let totalLicencias = Math.round((utilizados + licenciasPasadas) * 10) / 10;
        let balance = Math.round(((diasAsignadosOriginal + acumulados) - totalLicencias - descanso - receso) * 10) / 10;
        
        let elemLicencias = document.getElementById('lbl-dias-licencias');
        if (elemLicencias) {
            elemLicencias.textContent = totalLicencias;
        }
        let totalAusenta = 0;
        
        if (balance > 0) {
            actualizarClases('boleta-etiqueta-saldo', 'SALDO DISPONIBLE (Sobrantes a la fecha):');
            actualizarClases('boleta-sobrantes', balance);
            
            document.getElementById('lbl-dias-disponibles').textContent = balance;
            document.getElementById('lbl-dias-excedidos').textContent = 0;
            document.getElementById('caja-excedidos').classList.remove('excedida');
            
            totalAusenta = balance;
            
            let rango = calcularRangoVacacionesBolivia(fechaInicioObj, totalAusenta);
            actualizarClases('boleta-fecha-inicio', formatearFecha(rango.inicio));
            actualizarClases('boleta-fecha-conclusion', formatearFecha(rango.conclusion));
            actualizarClases('boleta-fecha-retorno', formatearFecha(rango.retorno));
            actualizarClases('boleta-hora-retorno', rango.horaRetorno);
            actualizarClases('boleta-jornada', rango.etiquetaJornada);

            document.querySelectorAll('.parrafo-vacaciones, #parrafo-vacaciones').forEach(el => el.style.display = 'block');
            document.querySelectorAll('.parrafo-no-vacaciones, #parrafo-no-vacaciones').forEach(el => el.style.display = 'none');
        } else {
            let excedidos = Math.round(Math.abs(balance) * 10) / 10;
            actualizarClases('boleta-etiqueta-saldo', 'DÍAS EXCEDIDOS:');
            actualizarClases('boleta-sobrantes', excedidos);
            
            document.getElementById('lbl-dias-disponibles').textContent = 0;
            document.getElementById('lbl-dias-excedidos').textContent = excedidos;
            if (excedidos > 0) {
                document.getElementById('caja-excedidos').classList.add('excedida');
            } else {
                document.getElementById('caja-excedidos').classList.remove('excedida');
            }
            
            totalAusenta = 0;
            
            actualizarClases('boleta-fecha-conclusion', '--/--/----');
            actualizarClases('boleta-fecha-retorno', '--/--/----');
            actualizarClases('boleta-hora-retorno', '--:--');
            actualizarClases('boleta-jornada', 'Jornada Completa');

            document.querySelectorAll('.parrafo-vacaciones, #parrafo-vacaciones').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.parrafo-no-vacaciones, #parrafo-no-vacaciones').forEach(el => el.style.display = 'block');
        }
        
        // El total de días que el docente se ausenta es exactamente su saldo a favor
        actualizarClases('boleta-total', totalAusenta);
        actualizarClases('boleta-total-texto', formatearDiasTexto(totalAusenta));

        if (autoGuardar) {
            guardarConfiguracionBoleta();
        }
    }

    // Inicializar cálculo al cargar y verificar si hay datos guardados
    document.addEventListener('DOMContentLoaded', function() {
        calcularVacaciones(false);
        try {
            ['inp-gestion', 'inp-fecha-inicio', 'inp-acumulados', 'inp-licencias-pasadas', 'inp-pendientes', 'inp-descanso', 'inp-receso'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', function() { calcularVacaciones(true); });
                    el.addEventListener('change', function() { calcularVacaciones(true); });
                    el.addEventListener('blur', function() { calcularVacaciones(true); });
                }
            });

            if (localStorage.getItem(storageKeyBoleta)) {
                let btnRestaurar = document.getElementById('btn-restaurar-boleta');
                if (btnRestaurar) {
                    btnRestaurar.style.borderColor = 'var(--azul-marino)';
                    btnRestaurar.setAttribute('title', 'Hay modificaciones anteriores guardadas para este docente. Haga clic para cargarlas.');
                }
            }
        } catch(e) {}
    });

    function actualizarFechaImpresion() {
        var ahora = new Date();
        var d = String(ahora.getDate()).padStart(2, '0');
        var m = String(ahora.getMonth() + 1).padStart(2, '0');
        var y = ahora.getFullYear();
        var hh = String(ahora.getHours()).padStart(2, '0');
        var mm = String(ahora.getMinutes()).padStart(2, '0');
        var textoFecha = d + '/' + m + '/' + y + ' ' + hh + ':' + mm;
        actualizarClases('boleta-fecha-impresion', textoFecha);
    }

    function imprimirBoleta() {
        calcularVacaciones(true);
        guardarConfiguracionBoleta();
        var tituloOriginal = document.title;
        document.title = ' ';
        actualizarFechaImpresion();
        window.print();
        setTimeout(function () {
            document.title = tituloOriginal;
        }, 1500);
    }

    var tituloPrevioImpresion = document.title;
    window.addEventListener('beforeprint', function () {
        calcularVacaciones(true);
        tituloPrevioImpresion = document.title;
        document.title = ' ';
        actualizarFechaImpresion();
    });
    window.addEventListener('afterprint', function () {
        document.title = tituloPrevioImpresion;
    });

    function crearPdfDesdeCanvas(canvas) {
        var base64 = canvas.toDataURL('image/jpeg', 0.95).split(',')[1];
        var binario = atob(base64);
        var imagen = new Uint8Array(binario.length);
        for (var i = 0; i < binario.length; i++) {
            imagen[i] = binario.charCodeAt(i);
        }

        var paginaAncho = 612;
        var paginaAlto = 792;
        var margen = 28;
        var anchoImg = paginaAncho - margen * 2;
        var altoImg = anchoImg * canvas.height / canvas.width;
        if (altoImg > paginaAlto - margen * 2) {
            altoImg = paginaAlto - margen * 2;
            anchoImg = altoImg * canvas.width / canvas.height;
        }
        var posX = (paginaAncho - anchoImg) / 2;
        var posY = paginaAlto - margen - altoImg;
        var contenido = 'q ' + anchoImg.toFixed(2) + ' 0 0 ' + altoImg.toFixed(2) + ' ' + posX.toFixed(2) + ' ' + posY.toFixed(2) + ' cm /Im0 Do Q';

        var partes = [];
        var longitud = 0;
        var posiciones = [];
        function aBytes(texto) {
            var bytes = new Uint8Array(texto.length);
            for (var k = 0; k < texto.length; k++) {
                bytes[k] = texto.charCodeAt(k) & 255;
            }
            return bytes;
        }
        function agregar(dato) {
            var bytes = typeof dato === 'string' ? aBytes(dato) : dato;
            partes.push(bytes);
            longitud += bytes.length;
        }
        function objeto(numero, cuerpo) {
            posiciones[numero] = longitud;
            agregar(numero + ' 0 obj\n');
            cuerpo();
            agregar('\nendobj\n');
        }

        agregar('%PDF-1.4\n');
        objeto(1, function () { agregar('<< /Type /Catalog /Pages 2 0 R >>'); });
        objeto(2, function () { agregar('<< /Type /Pages /Kids [3 0 R] /Count 1 >>'); });
        objeto(3, function () {
            agregar('<< /Type /Page /Parent 2 0 R /MediaBox [0 0 ' + paginaAncho + ' ' + paginaAlto + '] /Resources << /XObject << /Im0 4 0 R >> >> /Contents 5 0 R >>');
        });
        objeto(4, function () {
            agregar('<< /Type /XObject /Subtype /Image /Width ' + canvas.width + ' /Height ' + canvas.height + ' /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length ' + imagen.length + ' >>\nstream\n');
            agregar(imagen);
            agregar('\nendstream');
        });
        objeto(5, function () {
            agregar('<< /Length ' + contenido.length + ' >>\nstream\n' + contenido + '\nendstream');
        });

        var inicioXref = longitud;
        var xref = 'xref\n0 6\n0000000000 65535 f \n';
        for (var n = 1; n <= 5; n++) {
            xref += ('0000000000' + posiciones[n]).slice(-10) + ' 00000 n \n';
        }
        agregar(xref + 'trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n' + inicioXref + '\n%%EOF');

        return new Blob(partes, { type: 'application/pdf' });
    }

    function descargarImagenBoleta() {
        calcularVacaciones(true);
        guardarConfiguracionBoleta();
        if (typeof html2canvas === 'undefined') {
            alert('Cargando componente de descarga, intente de nuevo en un segundo.');
            return;
        }

        var btn = document.getElementById('btn-descargar-img');
        var textoOriginal = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Generando PDF...';

        actualizarFechaImpresion();

        var copiaOriginal = document.querySelector('#boleta-impresion .copia-boleta');
        if (!copiaOriginal) {
            btn.disabled = false;
            btn.innerHTML = textoOriginal;
            return;
        }

        var contenedorClon = document.createElement('div');
        contenedorClon.id = 'hoja-descarga-pdf-temp';
        contenedorClon.style.position = 'fixed';
        contenedorClon.style.left = '-9999px';
        contenedorClon.style.top = '0';
        contenedorClon.style.width = '800px';
        contenedorClon.style.backgroundColor = '#ffffff';
        contenedorClon.style.padding = '16px 20px';
        contenedorClon.style.boxSizing = 'border-box';
        contenedorClon.style.zIndex = '-1000';
        contenedorClon.style.fontFamily = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
        contenedorClon.style.color = '#000000';

        var clon = copiaOriginal.cloneNode(true);
        clon.style.height = 'auto';
        clon.style.display = 'block';
        clon.style.boxSizing = 'border-box';
        clon.style.padding = '8px 4px 12px 4px';
        clon.style.borderBottom = 'none';
        clon.style.marginBottom = '0';
        contenedorClon.appendChild(clon);

        document.body.appendChild(contenedorClon);

        html2canvas(contenedorClon, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff'
        }).then(function (canvas) {
            var enlace = document.createElement('a');
            var nombreDocente = '{{ Str::slug($docente->nombre_completo ?? "docente") }}';
            var urlPdf = URL.createObjectURL(crearPdfDesdeCanvas(canvas));
            enlace.download = 'boleta_vacaciones_' + nombreDocente + '.pdf';
            enlace.href = urlPdf;
            document.body.appendChild(enlace);
            enlace.click();
            document.body.removeChild(enlace);
            setTimeout(function () { URL.revokeObjectURL(urlPdf); }, 4000);
        }).catch(function (err) {
            console.error(err);
            alert('No se pudo generar el PDF de la boleta.');
        }).finally(function () {
            if (document.body.contains(contenedorClon)) {
                document.body.removeChild(contenedorClon);
            }
            btn.disabled = false;
            btn.innerHTML = textoOriginal;
        });
    }
</script>
<script src="{{ asset('js/html2canvas.min.js') }}"></script>
@endif
    @endif
@endif
