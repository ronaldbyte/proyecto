# MANUAL DE USUARIO OFICIAL UNIFICADO
## SISTEMA WEB DE GESTIÓN DE LICENCIAS Y VACACIONES DOCENTES
### UNIVERSIDAD NACIONAL "SIGLO XX" (UNSXX)
**Dirección General de Personal · Llallagua · Potosí · Bolivia**

---

## ÍNDICE GENERAL DEL DOCUMENTO
1. [Información General y Base Reglamentaria](#1-información-general-y-base-reglamentaria)
2. [Capítulo I: Guía del Docente](#2-capítulo-i-guía-del-docente)
3. [Capítulo II: Guía de Directores (Carrera y Área)](#3-capítulo-ii-guía-de-directores-carrera-y-área)
4. [Capítulo III: Guía de Jefatura de Personal (Administrador)](#4-capítulo-iii-guía-de-jefatura-de-personal-administrador)
5. [Preguntas Frecuentes (FAQ) y Soporte Técnico](#5-preguntas-frecuentes-faq-y-soporte-técnico)

---

## 1. INFORMACIÓN GENERAL Y BASE REGLAMENTARIA

### 1.1. Propósito de la Plataforma
El presente sistema web automatiza y digitaliza el circuito completo de solicitudes de licencias, permisos y cómputo de vacaciones de los docentes de la **Universidad Nacional "Siglo XX" (UNSXX)**. Reemplaza los formularios manuales en papel por un entorno transparente con trazabilidad digital, notificaciones automáticas y actualización en tiempo real de saldos vacacionales.

* **Dirección Web Oficial (Servidor en la Nube):** `https://licencias-unsxx26.onrender.com`
* **Acceso en Red Interna / Servidor Local:** `http://127.0.0.1:8000`

---

### 1.2. Escala Legal de Vacaciones por Antigüedad
El sistema calcula los días anuales de vacación de manera automática a partir de la **Fecha de Ingreso** institucional de cada docente, en estricto cumplimiento de la normativa laboral y el reglamento universitario:

| Antigüedad Laboral en la UNSXX | Días Hábiles de Vacación Anual | Condición Reglamentaria |
| :--- | :---: | :--- |
| **De 0 a 1 año cumplido** | **0 días** | Periodo de acumulación. No genera derecho hasta cumplir 1 año y 1 día. |
| **De 1 año y 1 día a 5 años cumplidos** | **15 días** | Primer escalafón de vacación anual reglamentaria. |
| **De 5 años y 1 día a 10 años cumplidos** | **20 días** | Segundo escalafón de vacación anual. |
| **Más de 10 años cumplidos y 1 día** | **30 días** | Escalafón máximo de vacación para personal docente titular. |
| **Modalidad a Contrato (Plazo Fijo)** | **0 días** | Los contratos eventuales no gozan de vacación anual acumulable. |

---

### 1.3. Cómputo de Jornadas Completas y Medios Días
El docente puede solicitar dos modalidades de tiempo:
* **Jornada Completa:** Ausencia por 1 o más días hábiles completos.
* **Medio Día (Mañana):** Ausencia en el turno de la mañana, computado exactamente como **0.5 días** laborables.
* **Medio Día (Tarde):** Ausencia en el turno de la tarde, computado exactamente como **0.5 días** laborables.

*(Dos medios días aprobados descuentan exactamente 1.0 día del saldo disponible).*

---

### 1.4. Flujo General de Tramitación entre los 3 Usuarios
```
┌─────────────────┐       Solicitud       ┌────────────────────────┐
│  1. DOCENTE     │ ────────────────────► │ 2. DIRECTOR DE CARRERA │
└─────────────────┘                       └────────────────────────┘
         ▲                                            │ Visto Bueno (1ª Instancia)
         │                                            ▼
         │                                ┌────────────────────────┐
         │                                │    DIRECTOR DE ÁREA    │ (Si corresponde)
         │                                └────────────────────────┘
         │                                            │
         │          Aprobación Final                  ▼
         │       (Descuento de Vacaciones)┌────────────────────────┐
         └────────────────────────────────│ 3. JEFATURA DE PERSONAL│
                                          └────────────────────────┘
```

---

## 2. CAPÍTULO I: GUÍA DEL DOCENTE

Este capítulo describe todo lo que el docente necesita saber para operar su cuenta.

### 2.1. Ingreso al Sistema y Credenciales
1. Ingrese a la web: `https://licencias-unsxx26.onrender.com`.
2. En el campo de usuario, digite su **Código Único de Docente** (ej. `DOC001`) o su **Correo Electrónico Institucional**.
3. Ingrese su **Contraseña** personal y presione **"Iniciar sesión"**.
4. *(En el primer inicio, si el sistema lo requiere, se le solicitará actualizar su contraseña por motivos de seguridad).*

### 2.2. Gestión de Mi Cuenta (Perfil y Contraseña)
* **Subir Foto de Perfil:** En la pestaña **"Mi perfil"**, seleccione una foto en formato JPG o PNG (máximo 2 MB) y presione **"Guardar foto de perfil"**.
* **Quitar Foto:** Si desea volver a la silueta por defecto, presione el botón **"✕ Quitar foto y usar silueta por defecto"**. La foto se eliminará directamente sin confirmaciones intermedias.
* **Cambiar Contraseña:** En **"Configuración"** > **"Cambiar contraseña"**, ingrese su contraseña actual y la nueva clave (mínimo 6 caracteres).

### 2.3. Consulta de Saldo de Vacaciones
En la parte superior de su pantalla principal, el docente visualiza su tarjeta oficial:
* **Días asignados:** Días correspondientes según su antigüedad calculada desde su fecha de ingreso.
* **Días utilizados:** Total de días consumidos en licencias aprobadas en la gestión.
* **Días disponibles:** Saldo neto actual disponible para solicitar.

### 2.4. Cómo Solicitar una Licencia (Paso a Paso)
1. Presione el botón dorado **"Nueva solicitud"** o ingrese por el menú lateral **"Solicitudes"**.
2. **Fecha de inicio y fin:** Seleccione las fechas correspondientes. Para una licencia de un solo día o medio día, elija la misma fecha en ambos campos.
3. **Jornada:**
   * Seleccione `Jornada completa` para días enteros.
   * Seleccione `Solo la mañana` si el permiso es únicamente por la media jornada de la mañana.
   * Seleccione `Solo la tarde` si el permiso es únicamente por la media jornada de la tarde.
4. **Motivo:** Escriba la causa de la solicitud (asuntos de salud, motivos académicos, diligencias personales, etc.).
5. Presione el botón **"Enviar solicitud"**. La solicitud ingresa inmediatamente al sistema de forma directa.

### 2.5. Seguimiento y Cancelación de Solicitudes
* **Pestaña "Pendientes":** Permite verificar en qué estado se encuentra la solicitud (Pendiente de Carrera, Pendiente de Área o Pendiente de Jefatura).
* **Cancelar Solicitud Errónea:** Si el docente cometió un error en las fechas o ya no requiere la licencia, puede ingresar a la solicitud mientras **no haya sido resuelta por Jefatura** y presionar el botón rojo **"Cancelar solicitud"** indicando el motivo. La solicitud se cancelará sin afectar su saldo.

### 2.6. Impresión del Historial Oficial de Licencias
A diferencia de una boleta de vacaciones físicas, este documento es una **lista directa oficial** para control y descargo:
1. Diríjase a la sección **"Solicitudes"** > Pestaña **"Historial"**.
2. Presione el botón dorado **"Imprimir historial de licencias"**.
3. Se abrirá la vista de impresión simplificada:
   * **Parte Superior:** Escudo oficial de la UNSXX y únicamente el **Nombre del Docente**.
   * **Lista de Solicitudes:** Muestra exclusivamente las solicitudes **Aprobadas** y **Rechazadas** con su periodo, días o medio día, motivo, estado, la autoridad que emitió la resolución (**"Decidido por"**) y la **Fecha y Hora** exacta del dictamen.
   * **Sin firmas ni sellos:** Formato limpio y directo listo para guardar en PDF (`Ctrl + P`) o archivar.

---

## 3. CAPÍTULO II: GUÍA DE DIRECTORES (CARRERA Y ÁREA)

Este capítulo describe las atribuciones y el procedimiento para los Directores de Carrera y Directores de Área.

### 3.1. Ingreso con Perfil de Director
1. Ingrese a la plataforma con su código asignado:
   * **Director de Carrera:** Código con prefijo `DRC...` (ej. `DRC001`) o correo institucional.
   * **Director de Área:** Código con prefijo `DRA...` (ej. `DRA001`) o correo institucional.
2. En el menú lateral **"Solicitudes"** verá una alerta numérica indicando la cantidad de solicitudes que aguardan su revisión.

### 3.2. Evaluación de Solicitudes en Primera Instancia
1. Ingrese a la pestaña **"Pendientes"** y abra la solicitud del docente.
2. Inspeccione los detalles del trámite:
   * Nombre del docente y carrera.
   * Fechas requeridas, tipo de jornada (completa o medio día) y días totales.
   * Motivo presentado y saldo de vacaciones del docente.
3. Al pie de página encontrará las opciones de decisión:
   * **Para Aprobar:** Seleccione **"Aprobar solicitud"** y presione **"Registrar decisión"**. La solicitud avanzará de inmediato a la siguiente instancia (Dirección de Área o Jefatura de Personal).
   * **Para Rechazar:** Seleccione **"Rechazar solicitud"**, escriba la fundamentación de la negativa en el recuadro de observaciones y presione **"Registrar decisión"**. El trámite quedará cerrado en estado Rechazada.
4. Las decisiones son directas e inmediatas, sin ventanas emergentes de confirmación.

### 3.3. Solicitud de Licencia Propia del Director
Los directores también gozan del derecho a solicitar licencias. Al presionar **"Nueva solicitud"**, el sistema enrutará su solicitud de manera automática según la jerarquía:
* La solicitud de un **Director de Carrera** pasará a revisión de su **Director de Área**.
* La solicitud de un **Director de Área** pasará directamente a **Jefatura de Personal**.

---

## 4. CAPÍTULO III: GUÍA DE JEFATURA DE PERSONAL (ADMINISTRADOR)

Este capítulo describe la administración completa del sistema a cargo de la Jefatura de Personal.

### 4.1. Ingreso a la Cuenta de Jefatura
* Ingrese con el código maestro `JEF001` (o su correo institucional `jefatura@unsxx.bo`) y su contraseña.
* La Jefatura dispone de control total sobre el padrón docente, las aprobaciones finales, configuraciones y base de datos.

### 4.2. Administración del Padrón Docente
Desde el menú lateral **"Docentes"**:

#### A) Registrar Nuevo Docente:
1. Presione el botón **"Nuevo docente"**.
2. Complete los campos requeridos:
   * Nombres y Apellidos completos.
   * Cédula de Identidad (C.I.), Correo electrónico y Teléfono celular.
   * **Fecha de Ingreso Laboral:** *(Dato crucial: a partir de esta fecha el sistema calculará automáticamente la antigüedad exacta y asignará los días de vacación según reglamento).*
   * **Tipo de Contrato:** Elija entre `Docente Titular`, `Docente Interino` o `Docente a Contrato`.
   * **Carrera y Área:** Asignación académica.
   * **Rol:** Seleccione `Docente`, `Director de Carrera` o `Director de Área`.
3. Presione **"Guardar docente"**. El sistema generará su código único automático para que el docente pueda ingresar.

#### B) Editar o Modificar Docente:
* Permite corregir nombres, actualizar la fecha de ingreso, reasignar carrera o cambiar el tipo de contrato.

#### C) Activar o Desactivar Docente:
* En la ficha del docente, presione **"Desactivar"** para suspender su acceso al sistema sin borrar sus datos históricos. Presione **"Activar"** para rehabilitarlo. El cambio es directo e instantáneo.

#### D) Eliminar Docente Definitivamente:
* Utilice el botón rojo **"Eliminar definitivamente"** solo si necesita purgar un registro duplicado o creado por error. Esta acción solicita confirmación explícita para proteger la integridad de los datos.

### 4.3. Decisión Final de Licencias y Descuento de Vacaciones
Desde el menú **"Solicitudes"** > Pestaña **"Pendientes de Jefatura"**:
1. Abra la solicitud que cuenta con el visto bueno de carrera/área.
2. Revise el historial de revisiones previas.
3. Tome la decisión final:
   * **Aprobar definitivamente:** Presione el botón verde **"Aprobar definitivamente"**. La solicitud pasa a estado `Aprobada`, **se descuentan automáticamente los días del saldo del docente** y se notifica al interesado.
   * **Rechazar solicitud:** Ingrese la observación y presione **"Rechazar solicitud"**. La solicitud pasa a estado `Rechazada` sin afectar el saldo de vacaciones.

### 4.4. Avisos al Celular (WhatsApp / SMS)
En **"Configuración"** > Pestaña **"Avisos al celular"**:
* La Jefatura puede registrar su número de teléfono institucional para recibir alertas automáticas cuando un docente radique solicitudes urgentes.

### 4.5. Copias de Seguridad (Respaldos) y Puesta a Punto
En el menú **"MI CUENTA"** > **"Copia de seguridad"**:
1. **Descargar Respaldo:** Presione **"Descargar respaldo de base de datos"** para obtener un archivo de seguridad de todos los docentes y solicitudes. Se recomienda realizar esta descarga de forma periódica.
2. **Restaurar Copia:** Permite cargar un archivo de respaldo previo en caso de reemplazo de servidor o contingencia técnica.
3. **Herramienta "Limpiar datos de prueba" (Inicio de Gestión Oficial):**
   * Ubicada en la parte inferior de la pantalla de respaldos.
   * Al hacer clic, **se eliminan todas las solicitudes y docentes de prueba**, conservando intacta únicamente la cuenta de **Jefatura de Personal**.
   * Esto permite iniciar el ciclo académico oficial con la base de datos totalmente limpia y lista para registrar al plantel docente real.

---

## 5. PREGUNTAS FRECUENTES (FAQ) Y SOPORTE TÉCNICO

* **¿Cómo se descuentan los medios días solicitados?**  
  Cada medio día (turno mañana o tarde) equivale a 0.5 días. Al aprobarse dos licencias de medio día, el sistema descuenta 1.0 día entero de vacación.

* **¿Por qué a un docente le aparecen 0 días de vacación asignados?**  
  Ocurre cuando el docente tiene menos de 1 año y 1 día de antigüedad en la institución (está en periodo de acumulación), o cuando su contrato está registrado bajo la modalidad eventual a plazo fijo ("Docente a Contrato").

* **¿Cómo guardar el Historial de Licencias en PDF?**  
  En la pantalla de impresión, presione `Ctrl + P`, elija como impresora de destino **"Guardar como PDF"** y guarde el archivo en su equipo.

* **¿Se requiere confirmación para aprobar o rechazar licencias?**  
  No. Las operaciones cotidianas están configuradas para ejecutarse de forma **directa e inmediata**, ahorrando tiempo a directores y a la jefatura.

---
*Documento oficial elaborado para la Universidad Nacional "Siglo XX" (UNSXX) · Dirección de Personal · Gestión 2026.*
