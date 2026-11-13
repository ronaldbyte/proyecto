# MANUAL DE USUARIO OFICIAL
## SISTEMA WEB DE GESTIÓN DE LICENCIAS Y VACACIONES DOCENTES
### UNIVERSIDAD NACIONAL "SIGLO XX" (UNSXX)
**Dirección de Personal · Llallagua · Potosí · Bolivia**

---

## 1. INFORMACIÓN GENERAL Y BASE NORMATIVA

### 1.1. Propósito del Sistema
El **Sistema Web de Gestión de Licencias y Vacaciones Docentes** ha sido desarrollado para modernizar, agilizar y transparentar la tramitación de permisos, licencias laborales y vacaciones del plantel docente de la **Universidad Nacional "Siglo XX"**. 

Este sistema reemplaza los antiguos formularios manuales en papel por un flujo digital automatizado con notificaciones, cómputo en tiempo real de saldos y trazabilidad completa de cada decisión institucional.

### 1.2. Base Legal y Escala de Vacaciones
El cálculo del derecho a vacaciones se rige bajo la normativa laboral vigente y el reglamento universitario:

* **Antigüedad de 0 a 1 año cumplido:** No corresponde vacación remunerada (**0 días**). El docente se encuentra en periodo de acumulación de antigüedad laboral.
* **De 1 año cumplido y 1 día a 5 años cumplidos:** **15 días hábiles** de vacación anual.
* **De 5 años cumplidos y 1 día a 10 años cumplidos:** **20 días hábiles** de vacación anual.
* **Más de 10 años cumplidos y 1 día:** **30 días hábiles** de vacación anual.
* **Condición de Contrato:**
  * **Docentes Titulares e Interinos:** Gozan del derecho a vacaciones según su antigüedad laboral acumulada.
  * **Docentes a Contrato / Plazo Fijo:** La normativa establece que los contratos temporales no acumulan días de vacación anual regular hasta consolidar la continuidad institucional reglamentaria.

### 1.3. Cómputo de Días y Medios Días
El sistema permite solicitar permisos fraccionados:
* **Jornada Completa:** Días enteros (ej. 1 día, 2 días, etc.).
* **Medio Día (Mañana):** Equivale a **0.5 días** laborables en turno matutino.
* **Medio Día (Tarde):** Equivale a **0.5 días** laborables en turno vespertino.

### 1.4. Acceso al Sistema
La plataforma está disponible en la web oficial:
* **Enlace Oficial (Producción en la Nube):** `https://licencias-unsxx26.onrender.com`
* **Acceso Local (Red Interna):** `http://127.0.0.1:8000`

---

## 2. ROLES DE USUARIO Y JERARQUÍA

El sistema contempla cuatro roles con responsabilidades bien diferenciadas:

```
[ Docente ] 
     │
     ▼ (Solicitud de licencia)
[ Director de Carrera ] ──(Aprobación 1ª Instancia)──► [ Jefatura de Personal ] (Decisión Final y Descuento)
     │                                                           ▲
     ▼ (Si la carrera depende directo de área o es Director)      │
[ Director de Área ] ────(Aprobación 2ª Instancia)───────────────┘
```

1. **Docente:** Solicita licencias, consulta su saldo de vacaciones, da seguimiento a sus solicitudes y puede imprimir su historial oficial de licencias aprobadas y rechazadas.
2. **Director de Carrera:** Revisa y emite decisión previa (aprobación o rechazo) sobre las solicitudes presentadas por los docentes adscritos a su carrera.
3. **Director de Área:** Revisa las solicitudes de los directores de carrera de su área o de carreras dependientes, emitiendo su aprobación o rechazo previo.
4. **Jefatura de Personal:** Instancia máxima y administradora del sistema. Registra y administra docentes, emite la **decisión final** (la cual descuenta automáticamente los días del saldo en caso de aprobación), configura avisos, descarga respaldos y realiza el mantenimiento de la base de datos.

---

## 3. INICIO DE SESIÓN Y GESTIÓN DE LA CUENTA

### 3.1. Identificación y Credenciales
Para ingresar al sistema, diríjase a la pantalla de bienvenida y complete:
* **Usuario:** Puede ingresar con su **Código Único de Usuario** (ejemplo: `DOC001`, `DRC001`, `DRA001`, `JEF001`) o con su **Correo Electrónico Institucional** registrado.
* **Contraseña:** Su clave de acceso personal.
* Presione el botón **"Iniciar sesión"**.

> [!NOTE]
> Si es la primera vez que ingresa al sistema y su cuenta tiene habilitada la política de cambio obligatorio de clave, el sistema le solicitará cambiar su contraseña antes de poder navegar.

### 3.2. Mi Perfil y Foto de Identificación
Ubicado en el menú superior o en la pestaña **"Mi perfil"**:
1. Podrá visualizar su Código, C.I., Cargo, Carrera, Área y Correo Electrónico.
2. **Subir Foto:** Puede seleccionar un archivo de imagen (JPG o PNG de hasta 2 MB) y presionar **"Guardar foto de perfil"**.
3. **Quitar Foto:** Si prefiere no usar foto propia, presione **"✕ Quitar foto y usar silueta por defecto"**. La foto se eliminará inmediatamente.

### 3.3. Cambio Voluntario de Contraseña
1. Vaya a la sección **"Configuración"** > Pestaña **"Cambiar contraseña"**.
2. Ingrese su contraseña actual.
3. Ingrese su nueva contraseña (mínimo 6 caracteres) y confírmela.
4. Presione **"Actualizar contraseña"**.

---

## 4. GUÍA DEL DOCENTE: TRÁMITE DE LICENCIAS

### 4.1. Panel Principal del Docente
Al ingresar, el docente dispone de:
* **Tarjeta de Vacaciones:** Indica los días anuales asignados según su fecha de ingreso, los días ya utilizados en licencias previas y los **días hábiles disponibles**.
* **Pestaña "Pendientes":** Muestra las solicitudes en curso (en revisión por carrera, área o jefatura).
* **Pestaña "Historial":** Muestra todas las solicitudes aprobadas, rechazadas o canceladas.

### 4.2. Cómo Crear una Nueva Solicitud de Licencia
1. Presione el botón dorado **"Nueva solicitud"** o el enlace del menú lateral.
2. Complete el formulario:
   * **Fecha de Inicio:** Fecha en que comenzará a hacer uso de la licencia.
   * **Fecha de Fin:** Fecha en que culminará la licencia (para un solo día, coloque la misma fecha).
   * **Jornada:**
     * `Jornada completa`: Para días completos.
     * `Solo la mañana`: Si solo se ausentará en el turno de la mañana.
     * `Solo la tarde`: Si solo se ausentará en el turno de la tarde.
   * **Motivo:** Escriba con claridad la causa de la solicitud (asuntos académicos, salud, diligencias personales, etc.).
3. Presione el botón **"Enviar solicitud"**.
4. La solicitud ingresará inmediatamente al sistema sin pantallas molestas de confirmación y el sistema le confirmará el registro con un aviso verde.

### 4.3. Cancelar una Solicitud Errónea
Si registró una solicitud con fechas equivocadas o ya no necesita la licencia:
1. Ingrese a la pestaña **"Pendientes"** y haga clic en **"Ver detalle"** de la solicitud.
2. Mientras la solicitud **no haya sido atendida por Jefatura**, encontrará al final el formulario **"Cancelar solicitud"**.
3. Ingrese el motivo de cancelación y presione el botón rojo **"Cancelar solicitud"**. La solicitud pasará inmediatamente al estado Cancelada y no afectará su saldo.

### 4.4. Imprimir Historial de Licencias
A diferencia de una boleta individual de vacaciones, el **Historial de Licencias** es un registro oficial directo de todas sus licencias resueltas:
1. Diríjase a la sección **"Historial"**.
2. Presione el botón dorado **"Imprimir historial de licencias"**.
3. Se abrirá una vista limpia optimizada para impresión (compatible con guardar como PDF o imprimir en papel):
   * **Parte superior:** Encabezado oficial de la UNSXX y el **Nombre del Docente**.
   * **Tabla de registros:** Detalla correlativo (N°), Periodo de licencia, Días solicitados (incluyendo "Medio día"), Motivo, Estado (Aprobada / Rechazada), la autoridad que tomó la resolución (**"Decidido por"**) y la **Fecha y Hora** exacta.
   * **Diseño sin firmas:** No requiere bloques de firmas ni datos sobrantes.

---

## 5. GUÍA PARA DIRECTORES (CARRERA Y ÁREA)

### 5.1. Revisión de Solicitudes Pendientes
1. Al ingresar con su cuenta de Director, el menú de **"Solicitudes"** mostrará un indicador numérico con las solicitudes pendientes de atención.
2. En la lista, haga clic en la solicitud que desea revisar.
3. Podrá inspeccionar:
   * Datos del docente solicitante y su antigüedad.
   * Fechas solicitadas, jornada y total de días.
   * Motivo presentado.
   * Saldo disponible del docente.

### 5.2. Aprobación o Rechazo en Primera Instancia
Al final de la página encontrará el panel de decisión:
* **Para Aprobar:** Seleccione la opción **"Aprobar solicitud"** y presione **"Registrar decisión"**. La solicitud avanzará a la siguiente etapa (Dirección de Área o Jefatura de Personal).
* **Para Rechazar:** Seleccione la opción **"Rechazar solicitud"**, escriba la observación o motivo de rechazo en el campo de texto y presione **"Registrar decisión"**.
* El registro es directo e instantáneo.

---

## 6. GUÍA PARA JEFATURA DE PERSONAL (ADMINISTRADOR)

### 6.1. Gestión del Padrón Docente
Desde el menú lateral **"Docentes"**:

#### A) Registrar un Nuevo Docente:
1. Presione el botón **"Nuevo docente"**.
2. Complete la información requerida:
   * **Nombres y Apellidos:** Nombre completo del docente.
   * **Cédula de Identidad (C.I.):** Número de documento de identidad.
   * **Correo electrónico:** Correo de contacto del docente.
   * **Teléfono:** Celular para notificaciones.
   * **Fecha de Ingreso:** Fecha exacta en la que inició labores en la UNSXX. *El sistema usará esta fecha para calcular automáticamente sus años de antigüedad y sus días de vacación según reglamento.*
   * **Tipo de Contrato:** Seleccione entre `Docente Titular`, `Docente Interino` o `Docente a Contrato`.
   * **Carrera y Área:** Asigne la carrera y área académica correspondiente.
   * **Rol:** Seleccione `Docente`, `Director de Carrera` o `Director de Área`.
3. Presione **"Guardar docente"**. El sistema generará automáticamente un **Código Único de Usuario** (ej. `DOC012`) con el que el docente podrá ingresar al sistema.

#### B) Editar o Modificar Docente:
* Ubique al docente en la lista y presione **"Editar"**.
* Podrá rectificar nombres, fecha de ingreso, tipo de contrato o carrera.

#### C) Activar / Desactivar Docente:
* En la ficha del docente, el botón **"Desactivar"** bloquea el acceso al sistema sin borrar sus registros históricos. Con el botón **"Activar"** se rehabilita de forma inmediata.

#### D) Eliminar Docente Definitivamente:
* En la ficha del docente, el botón rojo **"Eliminar definitivamente"** permite purgar un registro duplicado o erróneo. Esta acción solicita una confirmación explícita para evitar pérdidas accidentales.

---

### 6.2. Decisión Final de Licencias y Descuento de Vacaciones
Desde la pestaña **"Pendientes de Jefatura"**:
1. Haga clic sobre la solicitud pendiente.
2. Revise el historial de revisiones previas (aprobación de Dirección de Carrera o Área).
3. En el formulario de decisión final:
   * **Aprobar:** Presione **"Aprobar definitivamente"**. En ese mismo instante:
     * El estado pasa a `APROBADA`.
     * Se descuentan automáticamente los días solicitados del saldo del docente.
     * Se notifica al docente.
   * **Rechazar:** Ingrese la observación y presione **"Rechazar solicitud"**. No se descuenta ningún día del saldo.

---

### 6.3. Avisos al Celular (WhatsApp / SMS)
Ubicado en **"Configuración"** > Pestaña **"Avisos al celular"**:
* La Jefatura de Personal puede registrar su número de teléfono celular institucional.
* El sistema puede enviar alertas inmediatas cada vez que un docente radique una solicitud urgente que requiera atención de jefatura.

---

### 6.4. Copias de Seguridad y Puesta a Punto
Ubicado en el menú **"MI CUENTA"** > **"Copia de seguridad"**:

1. **Descargar Copia de Seguridad:**
   * Presione el botón **"Descargar respaldo de base de datos"**.
   * Se descargará un archivo con la fecha actual conteniendo todas las tablas, docentes, directores y solicitudes. Guarde este archivo periódicamente en un medio externo seguro.
2. **Restaurar Copia:**
   * Permite subir un archivo de respaldo generado con anterioridad para recuperar el sistema ante cualquier contingencia.
3. **Herramienta "Limpiar datos de prueba" (Inicio de Operaciones Oficiales):**
   * Diseñada para el momento en que se termine la fase de capacitación o pruebas.
   * Al ejecutarla, el sistema elimina todas las solicitudes y docentes de prueba, **dejando únicamente intacta la cuenta de Jefatura de Personal**.
   * Permite que la Jefatura empiece a ingresar a los docentes reales desde cero con una base de datos 100% limpia e impecable.

---

## 7. PREGUNTAS FRECUENTES (FAQ)

**P1: ¿Qué ocurre si un docente solicita medio día y luego otro medio día?**  
R: Cada medio día equivale a 0.5 días. Al acumular dos medios días aprobados, el sistema descontará exactamente 1.0 día entero de su saldo de vacaciones.

**P2: ¿Por qué a un docente le sale 0 días de vacación?**  
R: Esto puede deberse a dos razones reglamentarias:
1. El docente tiene una fecha de ingreso menor a 1 año cumplido más 1 día de antigüedad laboral (aún no ha generado derecho a su primera vacación anual).
2. El docente está registrado bajo la modalidad "Docente a Contrato" (plazo fijo), la cual por norma no goza de vacación acumulable.

**P3: ¿Cómo guardo el Historial de Licencias en formato PDF?**  
R: Al hacer clic en "Imprimir historial de licencias", presione las teclas `Ctrl + P` (o el botón Imprimir) y en la opción "Destino" elija **"Guardar como PDF"**. Obtendrá un documento limpio, sin publicidad, sin pie de página de navegador y listo para archivar o remitir digitalmente.

**P4: ¿Se requiere confirmación para aprobar o rechazar?**  
R: No. Todo el sistema ha sido optimizado con **procesamiento directo**, evitando ventanas emergentes innecesarias para brindar una experiencia de usuario rápida y fluida.

---

*Manual elaborado y verificado para la Universidad Nacional "Siglo XX" · Gestión Académica 2026.*
