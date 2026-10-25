@extends('layouts.acceso')
@section('titulo', 'Ingreso al sistema')
@section('contenido')
<div class="pantalla-acceso">
    <section class="acceso-foto">
        <div class="acceso-foto-texto">
            <h2>Universidad Nacional "Siglo XX"</h2>
            <p class="acceso-departamento">Departamento Jefatura de Personal Docente</p>
        </div>
    </section>
    <section class="acceso-formulario">
        <div class="tarjeta-acceso">
            <img class="escudo-login" src="{{ asset('imagenes/escudo.png') }}" alt="Escudo UNSXX">
            <h1>Gestión de Licencias</h1>
            <p class="subtitulo">Ingrese con su código institucional</p>

            @if(session('exito'))
                <div class="exito">{{ session('exito') }}</div>
            @endif
            @if($errors->any())
                <div class="errores">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('ingresar.enviar') }}">
                @csrf
                <div class="campo">
                    <label for="codigo">Código del docente</label>
                    <input id="codigo" name="codigo" value="{{ old('codigo') }}" required autofocus>
                </div>
                <div class="campo">
                    <label for="contrasena">Contraseña</label>
                    <input id="contrasena" type="password" name="contrasena" required>
                </div>
                <label style="display:flex;gap:8px;align-items:center;font-size:13px;margin:8px 0 16px;">
                    <input type="checkbox" name="recordarme" value="1"> Recordarme
                </label>
                <button class="boton boton-principal boton-bloque" type="submit">Ingresar</button>
            </form>

            <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--borde); text-align: center;">
                <button type="button" id="btn-abrir-modal" class="enlace-consulta">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    ¿No recuerdas tu código? Consúltalo con tu C.I.
                </button>
            </div>

            <p class="ayuda">Por una educación al servicio del pueblo.</p>
        </div>
    </section>
</div>

<!-- Modal para Consulta de Código Institucional -->
<div id="modal-consulta" class="modal-fondo" style="display: none;">
    <div class="modal-caja">
        <div class="modal-cabecera">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div class="icono-modal-circulo">🔍</div>
                <div>
                    <h3 style="margin: 0; font-size: 16px; color: var(--azul-noche); font-weight: 800;">Consultar Código de Acceso</h3>
                    <p style="margin: 2px 0 0; font-size: 12px; color: var(--gris);">Personal Docente y Directores · UNSXX</p>
                </div>
            </div>
            <button type="button" class="modal-cerrar" id="btn-cerrar-modal" title="Cerrar">&times;</button>
        </div>
        
        <div class="modal-cuerpo">
            <p style="font-size: 13px; color: #475569; margin: 0 0 14px; line-height: 1.45;">
                Ingresa tu número de <strong>Cédula de Identidad (C.I.)</strong> registrado para consultar tu código oficial de ingreso:
            </p>

            <form id="form-consulta-codigo" onsubmit="realizarConsultaCodigo(event)">
                <div style="display: flex; gap: 8px;">
                    <input 
                        type="text" 
                        id="inp-ci-consulta" 
                        placeholder="Ej: 8051662" 
                        required 
                        autocomplete="off"
                        class="input-ci"
                    >
                    <button type="submit" id="btn-consultar-submit" class="boton boton-principal" style="white-space: nowrap; padding: 10px 18px; font-weight: 600;">
                        Buscar
                    </button>
                </div>
            </form>

            <!-- Mensajes de estado -->
            <div id="consulta-cargando" style="display: none; text-align: center; padding: 20px 0; font-size: 13px; color: var(--gris);">
                <span>Buscando registros en el sistema...</span>
            </div>

            <div id="consulta-error" style="display: none; margin-top: 14px; padding: 12px 14px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; color: #b91c1c; font-size: 13px; line-height: 1.4;">
            </div>

            <!-- Contenedor de Resultados -->
            <div id="consulta-resultados" style="display: none; margin-top: 14px;">
            </div>
        </div>
    </div>
</div>

<style>
    .enlace-consulta {
        background: none;
        border: none;
        color: var(--azul-marino);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 4px 8px;
        border-radius: 6px;
        transition: background .2s, color .2s;
    }
    .enlace-consulta:hover {
        background: rgba(11, 42, 91, 0.08);
        color: var(--azul-medio);
        text-decoration: underline;
    }
    .modal-fondo {
        position: fixed;
        inset: 0;
        background: rgba(7, 20, 51, 0.65);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        animation: fadeInModal .2s ease-out;
    }
    @keyframes fadeInModal {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .modal-caja {
        background: #ffffff;
        border-radius: 16px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 20px 50px rgba(7, 20, 51, 0.25);
        overflow: hidden;
        animation: scaleInModal .25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes scaleInModal {
        from { transform: scale(0.92); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .modal-cabecera {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid var(--borde);
        background: #f8fafc;
    }
    .icono-modal-circulo {
        width: 36px;
        height: 36px;
        background: #e0f2fe;
        color: #0369a1;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
    .modal-cerrar {
        background: none;
        border: none;
        font-size: 24px;
        color: #64748b;
        cursor: pointer;
        padding: 0 4px;
        line-height: 1;
        border-radius: 6px;
        transition: color .2s, background .2s;
    }
    .modal-cerrar:hover {
        color: #0f172a;
        background: #e2e8f0;
    }
    .modal-cuerpo {
        padding: 20px;
    }
    .input-ci {
        flex: 1;
        padding: 10px 14px;
        border: 1.5px solid var(--borde);
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
    .input-ci:focus {
        border-color: var(--azul-marino);
        box-shadow: 0 0 0 3px rgba(11, 42, 91, 0.12);
    }
    .tarjeta-docente-resultado {
        border: 1.5px solid #bfdbfe;
        background: #f0f7ff;
        border-radius: 12px;
        padding: 14px;
        margin-bottom: 12px;
        text-align: left;
    }
    .tarjeta-docente-resultado:last-child {
        margin-bottom: 0;
    }
    .badge-codigo {
        display: inline-block;
        background: var(--azul-marino);
        color: #ffffff;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: 1px;
        padding: 4px 14px;
        border-radius: 6px;
        font-family: monospace;
    }
</style>

<script>
    const modalConsulta = document.getElementById('modal-consulta');
    const btnAbrirModal = document.getElementById('btn-abrir-modal');
    const btnCerrarModal = document.getElementById('btn-cerrar-modal');
    const inpCiConsulta = document.getElementById('inp-ci-consulta');
    const consultaCargando = document.getElementById('consulta-cargando');
    const consultaError = document.getElementById('consulta-error');
    const consultaResultados = document.getElementById('consulta-resultados');
    const btnSubmit = document.getElementById('btn-consultar-submit');

    if (btnAbrirModal && modalConsulta) {
        btnAbrirModal.addEventListener('click', function () {
            modalConsulta.style.display = 'flex';
            consultaError.style.display = 'none';
            consultaResultados.style.display = 'none';
            inpCiConsulta.value = '';
            setTimeout(function () { inpCiConsulta.focus(); }, 100);
        });
    }

    if (btnCerrarModal && modalConsulta) {
        btnCerrarModal.addEventListener('click', function () {
            modalConsulta.style.display = 'none';
        });
    }

    if (modalConsulta) {
        modalConsulta.addEventListener('click', function (e) {
            if (e.target === modalConsulta) {
                modalConsulta.style.display = 'none';
            }
        });
    }

    function realizarConsultaCodigo(e) {
        e.preventDefault();
        const ci = inpCiConsulta.value.trim();
        if (!ci) return;

        consultaError.style.display = 'none';
        consultaResultados.style.display = 'none';
        consultaCargando.style.display = 'block';
        btnSubmit.disabled = true;

        fetch("{{ route('codigo.consultar') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            },
            body: JSON.stringify({ ci: ci })
        })
        .then(function (res) {
            return res.json().then(function (data) {
                return { ok: res.ok, status: res.status, data: data };
            });
        })
        .then(function (resultado) {
            consultaCargando.style.display = 'none';
            btnSubmit.disabled = false;

            if (!resultado.ok) {
                consultaError.textContent = resultado.data.mensaje || 'No se encontró ningún docente con ese C.I.';
                consultaError.style.display = 'block';
                return;
            }

            renderizarResultados(resultado.data.docentes);
        })
        .catch(function () {
            consultaCargando.style.display = 'none';
            btnSubmit.disabled = false;
            consultaError.textContent = 'Ocurrió un error al consultar el sistema. Intente nuevamente.';
            consultaError.style.display = 'block';
        });
    }

    function renderizarResultados(docentes) {
        consultaResultados.innerHTML = '';
        if (!docentes || docentes.length === 0) {
            consultaError.textContent = 'No se encontró ningún docente con ese C.I.';
            consultaError.style.display = 'block';
            return;
        }

        docentes.forEach(function (d) {
            const card = document.createElement('div');
            card.className = 'tarjeta-docente-resultado';

            if (!d.activo) {
                card.innerHTML = `
                    <div style="font-weight: bold; font-size: 14px; color: #1e293b;">${escapeHtml(d.nombre)}</div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">${escapeHtml(d.carrera)} · ${escapeHtml(d.cargo)}</div>
                    <div style="margin-top: 8px; padding: 6px 10px; background: #fee2e2; border-radius: 6px; font-size: 12px; color: #991b1b; font-weight: 600;">
                        Cuenta desactivada. Consulte con Jefatura de Personal.
                    </div>
                `;
            } else {
                card.innerHTML = `
                    <div style="font-weight: 700; font-size: 14px; color: #0f172a;">${escapeHtml(d.nombre)}</div>
                    <div style="font-size: 12px; color: #475569; margin-top: 2px;">${escapeHtml(d.carrera)} · <span style="font-weight:600; color:var(--azul-marino);">${escapeHtml(d.cargo)}</span></div>
                    
                    <div style="margin: 12px 0 10px; display: flex; align-items: center; justify-content: space-between; background: #ffffff; padding: 8px 12px; border-radius: 8px; border: 1px solid #dbeafe;">
                        <span style="font-size: 12px; color: #475569; font-weight: 600;">Código de ingreso:</span>
                        <span class="badge-codigo">${escapeHtml(d.codigo)}</span>
                    </div>

                    <button type="button" class="boton boton-principal boton-bloque" onclick="usarCodigo('${escapeHtml(d.codigo)}')" style="font-size: 13px; padding: 8px;">
                        Usar este código para ingresar →
                    </button>
                `;
            }

            consultaResultados.appendChild(card);
        });

        consultaResultados.style.display = 'block';
    }

    function usarCodigo(codigo) {
        const inputCodigo = document.getElementById('codigo');
        const inputContrasena = document.getElementById('contrasena');
        if (inputCodigo) {
            inputCodigo.value = codigo;
            modalConsulta.style.display = 'none';
            if (inputContrasena) {
                inputContrasena.focus();
            }
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }
</script>
@endsection
