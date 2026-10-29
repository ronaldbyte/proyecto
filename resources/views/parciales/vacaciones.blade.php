@php
    $docente = $docente ?? null;
@endphp
@if($docente)
    @if(! $docente->gozaVacaciones())
        <div class="alerta">No goza de vacaciones.</div>
    @else
<div class="rejilla rejilla-4">
    <div class="tarjeta metrica">
        <span>Días asignados</span>
        <strong>{{ $docente->dias_asignados }}</strong>
    </div>
    <div class="tarjeta metrica">
        <span>Licencias</span>
        <strong id="lbl-dias-licencias">{{ $docente->dias_utilizados }}</strong>
    </div>
    <div class="tarjeta metrica">
        <span>Días disponibles</span>
        <strong id="lbl-dias-disponibles">{{ $docente->dias_disponibles }}</strong>
    </div>
    <div class="tarjeta metrica" id="caja-excedidos">
        <span>Días excedidos</span>
        <strong id="lbl-dias-excedidos">{{ $docente->dias_excedidos }}</strong>
    </div>
</div>

@if(Auth::user()->esJefatura())
<div class="no-imprimir" style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--borde);">
    <h3 style="margin-bottom: 12px; font-size: 16px;">Configuración para Boleta de Impresión</h3>
    <div class="rejilla rejilla-3" style="gap: 12px; margin-bottom: 12px;">
        <div class="tarjeta" style="display:flex; flex-direction:column; gap:4px; padding:12px;">
            <span style="font-size:12px; color:var(--gris);">Gestión</span>
            <input type="text" id="inp-gestion" value="{{ (int) date('Y') - 1 }}-{{ date('Y') }}" onfocus="this.select()" style="padding:6px; border:1px solid var(--borde); border-radius:6px; font-size:14px; text-align:center;" oninput="calcularVacaciones()">
        </div>
        <div class="tarjeta" style="display:flex; flex-direction:column; gap:4px; padding:12px;">
            <span style="font-size:12px; color:var(--gris);">A partir de</span>
            <input type="date" id="inp-fecha-inicio" value="{{ (int) date('Y') + 1 }}-01-01" style="padding:6px; border:1px solid var(--borde); border-radius:6px; font-size:14px; text-align:center;" oninput="calcularVacaciones()">
        </div>
        <div class="tarjeta" style="display:flex; flex-direction:column; gap:4px; padding:12px;">
            <span style="font-size:12px; color:var(--gris);">Días Acumulados</span>
            <input type="number" step="0.5" id="inp-acumulados" min="0" max="100" value="0" placeholder="0" onfocus="this.select()" oninput="limpiarCero(this); calcularVacaciones()" style="padding:6px; border:1px solid var(--borde); border-radius:6px; font-size:14px; text-align:center; font-weight:bold; color:var(--azul-marino);">
        </div>
    </div>
    <div class="rejilla rejilla-3" style="gap: 12px;">
        <div class="tarjeta" style="display:flex; flex-direction:column; gap:4px; padding:12px;">
            <span style="font-size:12px; color:var(--gris);">Licencias pasadas</span>
            <input type="number" step="1" id="inp-licencias-pasadas" min="0" max="100" value="0" placeholder="0" onfocus="this.select()" oninput="limpiarCero(this); calcularVacaciones()" style="padding:6px; border:1px solid var(--borde); border-radius:6px; font-size:14px; text-align:center; font-weight:bold; color:var(--azul-marino);">
        </div>
        <div class="tarjeta" style="display:flex; flex-direction:column; gap:4px; padding:12px;">
            <span style="font-size:12px; color:var(--gris);">Descanso Pedagógico</span>
            <input type="number" step="0.5" id="inp-descanso" min="0" max="60" value="5" placeholder="0" onfocus="this.select()" oninput="limpiarCero(this); calcularVacaciones()" style="padding:6px; border:1px solid var(--borde); border-radius:6px; font-size:14px; text-align:center; font-weight:bold; color:var(--azul-marino);">
        </div>
        <div class="tarjeta" style="display:flex; flex-direction:column; gap:4px; padding:12px;">
            <span style="font-size:12px; color:var(--gris);">Receso Fin de Año</span>
            <input type="number" step="0.5" id="inp-receso" min="0" max="60" value="3" placeholder="0" onfocus="this.select()" oninput="limpiarCero(this); calcularVacaciones()" style="padding:6px; border:1px solid var(--borde); border-radius:6px; font-size:14px; text-align:center; font-weight:bold; color:var(--azul-marino);">
        </div>
    </div>
    <div style="margin-top: 16px; display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap;">
        <button class="boton boton-azul" type="button" id="btn-descargar-img" onclick="descargarImagenBoleta()" style="display:inline-flex; align-items:center; gap:6px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Descargar en imagen (3 copias)
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
                </table>

                <div class="parrafo-vacaciones" id="parrafo-vacaciones" style="font-size: 9px; text-align: justify; line-height: 1.35; margin-top: 5px; padding: 4px 6px; border: 1px solid #cbd5e1; background-color: #f8fafc; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                    El/la docente hará uso de sus descansos y vacaciones <strong>a partir del <span class="boleta-fecha-inicio">{{ date('d/m/Y') }}</span></strong>, debiendo retornar a sus funciones el <strong><span class="boleta-fecha-retorno">--/--/----</span></strong> en modalidad de <strong><span class="boleta-jornada">Jornada Completa</span></strong>.
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
                            <td style="border: 1px solid #000; padding: 2px 4px; text-align: right; font-weight: bold; width: 48px;">{{ $docente->dias_asignados }} d</td>
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

        @php
            $destinatariosCopias = [
                'ORIGINAL: JEFATURA DE PERSONAL',
                '1ra. COPIA: DIRECCIÓN DE ÁREA / CARRERA',
                '2da. COPIA: INTERESADO(A)'
            ];
        @endphp
        <!-- Pie de Boleta con Fecha de Impresión -->
        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 8px; color: #475569; margin-top: 6px; padding-top: 2px;">
            <span>Llallagua · Potosí · Bolivia &nbsp;·&nbsp; <strong style="color: #0f172a; letter-spacing: 0.3px;">{{ $destinatariosCopias[$i] ?? '' }}</strong></span>
            <span>Fecha de impresión: <strong class="boleta-fecha-impresion">{{ now()->format('d/m/Y H:i') }}</strong></span>
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

    function calcularRangoVacacionesBolivia(fechaStr, dias) {
        if (!fechaStr) return { inicio: null, retorno: null };
        let [y, m, d] = fechaStr.split('-').map(Number);
        let cursor = new Date(y, m - 1, d);

        // Si la fecha elegida cae fin de semana o feriado, avanzar al primer día hábil
        while (!esDiaHabilBolivia(cursor)) {
            cursor.setDate(cursor.getDate() + 1);
        }

        let fechaInicio = new Date(cursor.getTime());
        let diasEnteros = Math.floor(dias);

        if (diasEnteros <= 0) {
            return { inicio: fechaInicio, retorno: fechaInicio };
        }

        // Consumir únicamente los días hábiles laborales de vacación
        let diasConsumidos = 0;
        while (diasConsumidos < diasEnteros) {
            if (esDiaHabilBolivia(cursor)) {
                diasConsumidos++;
                if (diasConsumidos === diasEnteros) {
                    break;
                }
            }
            cursor.setDate(cursor.getDate() + 1);
        }

        // La fecha de retorno laboral es el primer día hábil siguiente
        cursor.setDate(cursor.getDate() + 1);
        while (!esDiaHabilBolivia(cursor)) {
            cursor.setDate(cursor.getDate() + 1);
        }

        return {
            inicio: fechaInicio,
            retorno: new Date(cursor.getTime())
        };
    }

    function formatearFecha(fecha) {
        if (!fecha) return '--/--/----';
        let d = fecha.getDate().toString().padStart(2, '0');
        let m = (fecha.getMonth() + 1).toString().padStart(2, '0');
        let y = fecha.getFullYear();
        return `${d}/${m}/${y}`;
    }

    function actualizarClases(clase, valor) {
        document.querySelectorAll('.' + clase).forEach(el => el.textContent = valor);
    }

    function calcularVacaciones() {
        let gestion = document.getElementById('inp-gestion') ? document.getElementById('inp-gestion').value : '';
        let acumulados = document.getElementById('inp-acumulados') ? (parseFloat(document.getElementById('inp-acumulados').value) || 0) : 0;
        let licenciasPasadas = document.getElementById('inp-licencias-pasadas') ? (parseInt(document.getElementById('inp-licencias-pasadas').value) || 0) : 0;
        if (licenciasPasadas < 0) licenciasPasadas = 0;
        if (licenciasPasadas > 100) licenciasPasadas = 100;
        let utilizados = diasUtilizadosOriginal;
        let descanso = document.getElementById('inp-descanso') ? (parseFloat(document.getElementById('inp-descanso').value) || 0) : 0;
        let receso = document.getElementById('inp-receso') ? (parseFloat(document.getElementById('inp-receso').value) || 0) : 0;
        let fechaInicioStr = document.getElementById('inp-fecha-inicio') ? document.getElementById('inp-fecha-inicio').value : '';
        
        // Actualizar UI general de datos impresos
        actualizarClases('boleta-gestion', gestion);
        actualizarClases('boleta-acumulados', acumulados);
        actualizarClases('boleta-utilizados', utilizados);
        actualizarClases('boleta-licencias-pasadas', licenciasPasadas);
        actualizarClases('boleta-descanso', descanso);
        actualizarClases('boleta-receso', receso);
        
        let totalLicencias = utilizados + licenciasPasadas;
        let balance = (diasAsignadosOriginal + acumulados) - totalLicencias - descanso - receso;
        
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
            
            document.querySelectorAll('.parrafo-vacaciones, #parrafo-vacaciones').forEach(el => el.style.display = 'block');
            document.querySelectorAll('.parrafo-no-vacaciones, #parrafo-no-vacaciones').forEach(el => el.style.display = 'none');
        } else {
            let excedidos = Math.abs(balance);
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
            
            document.querySelectorAll('.parrafo-vacaciones, #parrafo-vacaciones').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.parrafo-no-vacaciones, #parrafo-no-vacaciones').forEach(el => el.style.display = 'block');
        }
        
        // El total de días que el docente se ausenta es exactamente su saldo a favor
        actualizarClases('boleta-total', totalAusenta);
        
        // Calcular fechas hábiles (excluyendo sábados, domingos y feriados oficiales de Bolivia)
        if (totalAusenta > 0) {
            let rango = calcularRangoVacacionesBolivia(fechaInicioStr, totalAusenta);
            actualizarClases('boleta-fecha-inicio', formatearFecha(rango.inicio));
            actualizarClases('boleta-fecha-retorno', formatearFecha(rango.retorno));
            
            let tieneMedioDia = (totalAusenta - Math.floor(totalAusenta)) > 0;
            actualizarClases('boleta-jornada', tieneMedioDia ? 'Medio Día (Tarde)' : 'Jornada Completa');
        }
    }

    // Inicializar cálculo al cargar
    document.addEventListener('DOMContentLoaded', calcularVacaciones);

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
        tituloPrevioImpresion = document.title;
        document.title = ' ';
        actualizarFechaImpresion();
    });
    window.addEventListener('afterprint', function () {
        document.title = tituloPrevioImpresion;
    });

    function descargarImagenBoleta() {
        if (typeof html2canvas === 'undefined') {
            alert('Cargando componente de imagen, intente de nuevo en un segundo.');
            return;
        }

        var btn = document.getElementById('btn-descargar-img');
        var textoOriginal = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Generando imagen (3 copias)...';

        actualizarFechaImpresion();

        var copias = document.querySelectorAll('#boleta-impresion .copia-boleta');
        if (!copias || copias.length === 0) {
            btn.disabled = false;
            btn.innerHTML = textoOriginal;
            return;
        }

        var contenedorClon = document.createElement('div');
        contenedorClon.id = 'hoja-descarga-3copias-temp';
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

        copias.forEach(function (copiaOriginal, index) {
            var clon = copiaOriginal.cloneNode(true);
            clon.style.height = 'auto';
            clon.style.display = 'block';
            clon.style.boxSizing = 'border-box';
            clon.style.padding = '8px 4px 12px 4px';
            if (index < copias.length - 1) {
                clon.style.borderBottom = '1.8px dashed #475569';
                clon.style.marginBottom = '14px';
            } else {
                clon.style.borderBottom = 'none';
                clon.style.marginBottom = '0';
            }
            contenedorClon.appendChild(clon);
        });

        document.body.appendChild(contenedorClon);

        html2canvas(contenedorClon, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff'
        }).then(function (canvas) {
            var enlace = document.createElement('a');
            var nombreDocente = '{{ Str::slug($docente->nombre_completo ?? "docente") }}';
            enlace.download = 'boleta_vacaciones_3copias_' + nombreDocente + '.png';
            enlace.href = canvas.toDataURL('image/png');
            enlace.click();
        }).catch(function (err) {
            console.error(err);
            alert('No se pudo generar la imagen con las 3 copias.');
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
