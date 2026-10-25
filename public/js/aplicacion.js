document.addEventListener('DOMContentLoaded', function () {
    var botonMenu = document.getElementById('abrir-menu');
    var barra = document.getElementById('barra-lateral');
    var fondo = document.getElementById('fondo-movil');
    if (botonMenu && barra) {
        function cerrarMenuMovil() {
            barra.classList.remove('abierto');
            if (fondo) fondo.classList.remove('activo');
        }
        botonMenu.addEventListener('click', function () {
            var abierto = barra.classList.toggle('abierto');
            if (fondo) fondo.classList.toggle('activo', abierto);
        });
        if (fondo) {
            fondo.addEventListener('click', cerrarMenuMovil);
        }
        barra.querySelectorAll('a, button.enlace-menu').forEach(function (el) {
            el.addEventListener('click', function () {
                if (window.innerWidth <= 980) {
                    cerrarMenuMovil();
                }
            });
        });
    }

    var inicio = document.getElementById('fecha_inicio');
    var fin = document.getElementById('fecha_fin');
    var dias = document.getElementById('dias_calculados');
    var aviso = document.getElementById('aviso-exceso');
    var campoFin = document.getElementById('campo-fecha-fin');
    var disponibles = dias ? parseFloat(dias.dataset.disponibles || '0') : 0;

    function jornadaElegida() {
        var marcada = document.querySelector('input[name="jornada"]:checked');
        return marcada ? marcada.value : 'completa';
    }

    function esMedioDia() {
        var jornada = jornadaElegida();
        return jornada === 'manana' || jornada === 'tarde';
    }

    function actualizarJornada() {
        if (campoFin && fin) {
            campoFin.hidden = esMedioDia();
            fin.required = !esMedioDia();
            if (esMedioDia() && inicio && inicio.value) {
                fin.value = inicio.value;
            }
        }
        calcular();
    }

    function calcular() {
        if (!inicio || !dias || !inicio.value) {
            return;
        }
        if (esMedioDia()) {
            if (fin) {
                fin.value = inicio.value;
            }
            dias.value = '0.5 día (medio día)';
            if (aviso) {
                aviso.hidden = 0.5 <= disponibles;
            }
            return;
        }
        if (!fin || !fin.value) {
            return;
        }
        var url = dias.dataset.url
            + '?fecha_inicio=' + encodeURIComponent(inicio.value)
            + '&fecha_fin=' + encodeURIComponent(fin.value)
            + '&jornada=' + encodeURIComponent(jornadaElegida());
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.dias !== undefined && data.dias !== null && !data.error) {
                    dias.value = data.dias + ' día(s)';
                    if (aviso) {
                        aviso.hidden = data.dias <= disponibles;
                    }
                } else if (data.error) {
                    dias.value = data.error;
                }
            });
    }

    if (inicio && fin) {
        inicio.addEventListener('change', calcular);
        fin.addEventListener('change', calcular);
    }

    document.querySelectorAll('input[name="jornada"]').forEach(function (opcion) {
        opcion.addEventListener('change', actualizarJornada);
    });
    if (document.querySelector('input[name="jornada"]')) {
        actualizarJornada();
    }

    var botonVolver = document.getElementById('boton-volver');
    if (botonVolver) {
        botonVolver.addEventListener('click', function () {
            var destino = botonVolver.getAttribute('data-panel') || '/panel';
            var origen = document.referrer;
            if (origen) {
                try {
                    var urlOrigen = new URL(origen);
                    if (urlOrigen.origin === window.location.origin && urlOrigen.pathname !== window.location.pathname) {
                        window.history.back();
                        return;
                    }
                } catch (error) {
                    //
                }
            }
            window.location.href = destino;
        });
    }

    document.querySelectorAll('[data-confirmar]').forEach(function (formulario) {
        formulario.addEventListener('submit', function (evento) {
            if (!window.confirm(formulario.getAttribute('data-confirmar'))) {
                evento.preventDefault();
            }
        });
    });

    var tipoContrato = document.getElementById('tipo_contrato');
    var fechaIngreso = document.getElementById('fecha_ingreso');
    var diasVista = document.getElementById('dias_asignados_vista');
    var vigenciaVista = document.getElementById('vigencia_contrato');

    function aniosDesde(fecha) {
        if (!fecha) {
            return 0;
        }
        var inicio = new Date(fecha + 'T00:00:00');
        var hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        var anios = hoy.getFullYear() - inicio.getFullYear();
        var mes = hoy.getMonth() - inicio.getMonth();
        if (mes < 0 || (mes === 0 && hoy.getDate() < inicio.getDate())) {
            anios--;
        }
        return Math.max(0, anios);
    }

    function diasSegunAntiguedad(tipo, anios) {
        if (tipo === 'consultor') {
            return 0;
        }
        if (anios > 10) {
            return 30;
        }
        if (anios >= 5) {
            return 20;
        }
        return 15;
    }

    function dos(n) {
        return n < 10 ? '0' + n : String(n);
    }

    function formatoFecha(fecha) {
        return dos(fecha.getDate()) + '/' + dos(fecha.getMonth() + 1) + '/' + fecha.getFullYear();
    }

    function vigenciaContrato(fecha) {
        if (!fecha) {
            return null;
        }
        var ingreso = new Date(fecha + 'T00:00:00');
        var hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        var aniversario = new Date(hoy.getFullYear(), ingreso.getMonth(), ingreso.getDate());
        var inicio;
        var fin;
        if (aniversario > hoy) {
            inicio = new Date(aniversario);
            inicio.setFullYear(inicio.getFullYear() - 1);
            fin = aniversario;
        } else {
            inicio = aniversario;
            fin = new Date(aniversario);
            fin.setFullYear(fin.getFullYear() + 1);
        }
        if (inicio < ingreso) {
            inicio = ingreso;
            fin = new Date(ingreso);
            fin.setFullYear(fin.getFullYear() + 1);
        }
        return formatoFecha(inicio) + ' al ' + formatoFecha(fin);
    }

    function actualizarDiasAsignados() {
        if (!tipoContrato || !diasVista) {
            return;
        }
        var tipo = tipoContrato.value;
        var anios = aniosDesde(fechaIngreso ? fechaIngreso.value : '');
        var dias = diasSegunAntiguedad(tipo, anios);
        if (vigenciaVista) {
            vigenciaVista.value = vigenciaContrato(fechaIngreso ? fechaIngreso.value : '') || '—';
        }
        if (!tipo) {
            diasVista.value = '—';
            return;
        }
        if (tipo === 'consultor') {
            diasVista.value = '0 días';
            return;
        }
        diasVista.value = anios + ' años · ' + dias + ' días';
    }

    if (tipoContrato) {
        tipoContrato.addEventListener('change', actualizarDiasAsignados);
        if (fechaIngreso) {
            fechaIngreso.addEventListener('change', actualizarDiasAsignados);
        }
        actualizarDiasAsignados();
    }

    var codigoInput = document.getElementById('codigo_asignado');
    var rolCodigo = document.getElementById('rol_id');
    if (codigoInput && rolCodigo && !codigoInput.getAttribute('data-fijo')) {
        rolCodigo.addEventListener('change', function () {
            var url = codigoInput.getAttribute('data-url');
            if (!url) {
                return;
            }
            fetch(url + '?rol_id=' + encodeURIComponent(rolCodigo.value), { headers: { Accept: 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.codigo) {
                        codigoInput.value = data.codigo;
                    }
                });
        });
    }

    var areaSelect = document.getElementById('area_id');
    var carreraSelect = document.getElementById('carrera_id');

    function filtrarCarrerasPorArea() {
        if (!areaSelect || !carreraSelect) {
            return;
        }
        var areaId = areaSelect.value;
        var valorActual = carreraSelect.value;
        Array.prototype.forEach.call(carreraSelect.querySelectorAll('optgroup'), function (grupo) {
            var visibles = 0;
            Array.prototype.forEach.call(grupo.querySelectorAll('option'), function (opcion) {
                var mismaArea = !areaId || opcion.getAttribute('data-area') === areaId;
                opcion.hidden = !mismaArea;
                opcion.disabled = !mismaArea;
                if (mismaArea) {
                    visibles++;
                }
            });
            grupo.hidden = visibles === 0;
        });
        var opcionElegida = carreraSelect.options[carreraSelect.selectedIndex];
        if (opcionElegida && opcionElegida.disabled) {
            carreraSelect.value = '';
        }
        if (valorActual && !carreraSelect.value) {
            carreraSelect.value = '';
        }
    }

    if (areaSelect && carreraSelect) {
        areaSelect.addEventListener('change', filtrarCarrerasPorArea);
        filtrarCarrerasPorArea();
    }

    var campana = document.getElementById('campana-notificaciones');
    function actualizarCampana() {
        if (!campana) {
            return;
        }
        var url = campana.getAttribute('data-estado');
        if (!url) {
            return;
        }
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var punto = campana.querySelector('.punto');
                var cantidad = parseInt(data.cantidad || 0, 10);
                if (cantidad > 0) {
                    if (!punto) {
                        punto = document.createElement('span');
                        punto.className = 'punto';
                        campana.appendChild(punto);
                    }
                    punto.textContent = cantidad;
                } else if (punto) {
                    punto.remove();
                }
            })
            .catch(function () {});
    }
    if (campana) {
        setInterval(actualizarCampana, 20000);
    }
});
