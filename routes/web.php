<?php

use App\Http\Controllers\AccesoController;
use App\Http\Controllers\AprobacionController;
use App\Http\Controllers\AvisosController;
use App\Http\Controllers\DocenteGestionController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\ImpresionController;
use App\Http\Controllers\JefaturaPersonalController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\RespaldoController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\SolicitudController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('ingresar'));

Route::middleware('guest')->group(function () {
    Route::get('/ingresar', [AccesoController::class, 'mostrarIngreso'])->name('ingresar');
    Route::post('/ingresar', [AccesoController::class, 'ingresar'])
        ->middleware('throttle:ingreso')
        ->name('ingresar.enviar');
    Route::post('/consultar-codigo', [AccesoController::class, 'consultarCodigo'])
        ->middleware('throttle:25,1')
        ->name('codigo.consultar');
});

Route::middleware(['auth', 'cambiar.contrasena'])->group(function () {
    Route::post('/salir', [AccesoController::class, 'salir'])->name('salir');
    Route::get('/perfil', [AccesoController::class, 'editarPerfil'])->name('perfil.editar');
    Route::post('/perfil/foto', [AccesoController::class, 'actualizarFoto'])->name('perfil.foto.actualizar');
    Route::post('/perfil/foto/eliminar', [AccesoController::class, 'eliminarFoto'])->name('perfil.foto.eliminar');
    Route::get('/contrasena', [AccesoController::class, 'editarContrasena'])->name('contrasena.editar');
    Route::post('/contrasena', [AccesoController::class, 'actualizarContrasena'])->name('contrasena.actualizar');

    Route::get('/panel', [PanelController::class, 'mostrar'])->name('panel');

    Route::get('/solicitudes', [SolicitudController::class, 'mias'])->name('solicitudes.mias');
    Route::get('/solicitudes/crear', [SolicitudController::class, 'crear'])->name('solicitudes.crear');
    Route::post('/solicitudes', [SolicitudController::class, 'guardar'])->name('solicitudes.guardar');
    Route::get('/solicitudes/calcular-dias', [SolicitudController::class, 'calcularDias'])->name('solicitudes.calcular');
    Route::get('/solicitudes/{solicitud}', [SolicitudController::class, 'ver'])->name('solicitudes.ver');
    Route::post('/solicitudes/{solicitud}/cancelar', [SolicitudController::class, 'cancelar'])->name('solicitudes.cancelar');

    Route::get('/historial', [HistorialController::class, 'propio'])->name('historial.propio');

    Route::get('/notificaciones', [NotificacionController::class, 'listar'])->name('notificaciones.listar');
    Route::get('/notificaciones/estado', [NotificacionController::class, 'estado'])->name('notificaciones.estado');
    Route::post('/notificaciones/todas', [NotificacionController::class, 'marcarTodas'])->name('notificaciones.todas');
    Route::post('/notificaciones/{notificacion}', [NotificacionController::class, 'marcarLeida'])->name('notificaciones.leer');

    Route::middleware('rol:director_carrera,director_area')->prefix('direccion')->group(function () {
        Route::get('/pendientes', [AprobacionController::class, 'pendientesDirector'])->name('director.pendientes');
        Route::get('/historial', [AprobacionController::class, 'historialDirector'])->name('director.historial');
        Route::post('/solicitudes/{solicitud}/decidir', [AprobacionController::class, 'decidirDirector'])->name('director.decidir');
    });

    Route::middleware('rol:jefatura_personal')->prefix('jefatura')->group(function () {
        Route::get('/docentes', [DocenteGestionController::class, 'listar'])->name('jefatura.docentes.listar');
        Route::get('/docentes/crear', [DocenteGestionController::class, 'crear'])->name('jefatura.docentes.crear');
        Route::get('/docentes/codigo', [DocenteGestionController::class, 'sugerirCodigo'])->name('jefatura.docentes.codigo');
        Route::post('/docentes', [DocenteGestionController::class, 'guardar'])->name('jefatura.docentes.guardar');
        Route::get('/docentes/{docente}', [DocenteGestionController::class, 'ver'])->name('jefatura.docentes.ver');
        Route::get('/docentes/{docente}/editar', [DocenteGestionController::class, 'editar'])->name('jefatura.docentes.editar');
        Route::put('/docentes/{docente}', [DocenteGestionController::class, 'actualizar'])->name('jefatura.docentes.actualizar');
        Route::post('/docentes/{docente}/estado', [DocenteGestionController::class, 'desactivar'])->name('jefatura.docentes.estado');
        Route::delete('/docentes/{docente}', [DocenteGestionController::class, 'eliminar'])->name('jefatura.docentes.eliminar');

        Route::get('/solicitudes', [AprobacionController::class, 'listarJefatura'])->name('jefatura.solicitudes.listar');
        Route::get('/solicitudes/pendientes', [AprobacionController::class, 'pendientesJefatura'])->name('jefatura.solicitudes.pendientes');
        Route::post('/solicitudes/{solicitud}/decidir', [AprobacionController::class, 'decidirJefatura'])->name('jefatura.decidir');

        Route::get('/historial/{docente}', [HistorialController::class, 'docente'])->name('jefatura.historial.docente');
        Route::get('/roles', [RolController::class, 'listar'])->name('jefatura.roles');
        Route::get('/personal', [JefaturaPersonalController::class, 'listar'])->name('jefatura.personal.listar');
        Route::get('/personal/crear', [JefaturaPersonalController::class, 'crear'])->name('jefatura.personal.crear');
        Route::post('/personal', [JefaturaPersonalController::class, 'guardar'])->name('jefatura.personal.guardar');
        Route::get('/personal/{docente}/editar', [JefaturaPersonalController::class, 'editar'])->name('jefatura.personal.editar');
        Route::put('/personal/{docente}', [JefaturaPersonalController::class, 'actualizar'])->name('jefatura.personal.actualizar');
        Route::get('/avisos', [AvisosController::class, 'editar'])->name('jefatura.avisos');
        Route::post('/avisos', [AvisosController::class, 'guardar'])->name('jefatura.avisos.guardar');
        Route::post('/avisos/probar', [AvisosController::class, 'probar'])->name('jefatura.avisos.probar');
        Route::get('/imprimir/docente/{docente}', [ImpresionController::class, 'docente'])->name('jefatura.imprimir.docente');
        Route::get('/imprimir/solicitudes', [ImpresionController::class, 'solicitudes'])->name('jefatura.imprimir.solicitudes');
        Route::get('/respaldo', [RespaldoController::class, 'index'])->name('jefatura.respaldo');
        Route::get('/respaldo/descargar', [RespaldoController::class, 'descargar'])->name('jefatura.respaldo.descargar');
        Route::post('/respaldo/restaurar', [RespaldoController::class, 'restaurar'])->name('jefatura.respaldo.restaurar');
    });
});
