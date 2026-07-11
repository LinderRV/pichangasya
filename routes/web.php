<?php

use App\Http\Controllers\Cliente\ClienteController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\PaginasController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\Usuario\UsuarioController;
use App\Http\Controllers\Admin\Rol\RolController;
use App\Http\Controllers\Admin\Complejo\ComplejoController;
use App\Http\Controllers\Admin\Complejo\AsignacionController;
use App\Http\Controllers\Admin\Cancha\CanchaController;
use App\Http\Controllers\Admin\Horario\HorarioController;
use App\Http\Controllers\Admin\Bloqueo\BloqueoController;
use App\Http\Controllers\Admin\Reserva\AdminReservaController;
use App\Http\Controllers\Admin\Pago\PagoController;
use App\Http\Controllers\Admin\Pago\MetodoPagoController;
use App\Http\Controllers\Admin\Reporte\ReporteReservaController;
use App\Http\Controllers\Admin\Reporte\ReporteIngresoController;
use App\Http\Controllers\Cliente\ClienteReservaController;

Route::get('/', [PaginasController::class, 'inicio'])->name('home');



Route::prefix('web')
->name('web.paginas.')
->controller(PaginasController::class)
->group(function () {
    Route::get('/inicio', 'inicio')->name('inicio');
    Route::get('/canchas', 'canchas')->name('canchas');
    Route::get('/cancha/{id}', 'cancha')->name('cancha');
    Route::get('/slots/{idCancha}/{fecha}', 'slots')->name('slots');
});




Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {

    Route::controller(UsuarioController::class)->group(function () {
        Route::prefix('admin')->group(function () {
            Route::prefix('usuarios')->group(function () {
                Route::get('/', [UsuarioController::class, 'index'])->name('admin.usuarios.index');
                Route::get('/lista', [UsuarioController::class, 'listaUsuario'])->name('admin.usuarios.lista');
                Route::post('/guardar', [UsuarioController::class, 'guardar'])->name('admin.usuarios.guardar');
                Route::get('/obtener/{id}', [UsuarioController::class, 'obtener'])->name('admin.usuarios.obtener');
                Route::put('/actualizar/{id}', [UsuarioController::class, 'actualizar'])->name('admin.usuarios.actualizar');
                Route::delete('/eliminar/{id}', [UsuarioController::class, 'eliminar'])->name('admin.usuarios.eliminar');
            });
            
        });
        
    });

    Route::controller(RolController::class)->group(function () {
        Route::prefix('admin')->group(function () {
            Route::prefix('rol')->group(function () {
                Route::get('/', [RolController::class, 'index'])->name('admin.rol.index');
                Route::get('/lista', [RolController::class, 'listaRol'])->name('admin.rol.lista');
                Route::post('/guardar', [RolController::class, 'guardar'])->name('admin.rol.guardar');
                Route::get('/obtener/{id}', [RolController::class, 'obtener'])->name('admin.rol.obtener');
                Route::put('/actualizar/{id}', [RolController::class, 'actualizar'])->name('admin.rol.actualizar');
                Route::delete('/eliminar/{id}', [RolController::class, 'eliminar'])->name('admin.rol.eliminar');
            });

        });

    });

    Route::prefix('admin/complejos')->group(function () {
        Route::get('/', [ComplejoController::class, 'index'])->name('admin.complejos.index');
        Route::get('/lista', [ComplejoController::class, 'listaComplejo'])->name('admin.complejos.lista');
        Route::get('/provincias/{idDepartamento}', [ComplejoController::class, 'provincias'])->name('admin.complejos.provincias');
        Route::get('/distritos/{idProvincia}', [ComplejoController::class, 'distritos'])->name('admin.complejos.distritos');
        Route::post('/guardar', [ComplejoController::class, 'guardar'])->name('admin.complejos.guardar');
        Route::get('/obtener/{id}', [ComplejoController::class, 'obtener'])->name('admin.complejos.obtener');
        Route::post('/actualizar/{id}', [ComplejoController::class, 'actualizar'])->name('admin.complejos.actualizar');
        Route::delete('/eliminar/{id}', [ComplejoController::class, 'eliminar'])->name('admin.complejos.eliminar');

        // Submódulo: Asignar Dueño (usuario_complejos)
        Route::get('/asignacion', [AsignacionController::class, 'index'])->name('admin.complejos.asignacion.index');
        Route::get('/asignacion/lista', [AsignacionController::class, 'lista'])->name('admin.complejos.asignacion.lista');
        Route::get('/asignacion/complejos-disponibles', [AsignacionController::class, 'complejosDisponibles'])->name('admin.complejos.asignacion.complejosDisponibles');
        Route::get('/asignacion/usuarios-disponibles', [AsignacionController::class, 'usuariosDisponibles'])->name('admin.complejos.asignacion.usuariosDisponibles');
        Route::post('/asignacion/guardar', [AsignacionController::class, 'guardar'])->name('admin.complejos.asignacion.guardar');
        Route::get('/asignacion/obtener/{id}', [AsignacionController::class, 'obtener'])->name('admin.complejos.asignacion.obtener');
        Route::put('/asignacion/actualizar/{id}', [AsignacionController::class, 'actualizar'])->name('admin.complejos.asignacion.actualizar');
        Route::delete('/asignacion/eliminar/{id}', [AsignacionController::class, 'eliminar'])->name('admin.complejos.asignacion.eliminar');
    });




    Route::prefix('admin/canchas')->group(function () {
        Route::get('/', [CanchaController::class, 'index'])->name('admin.canchas.index');
        Route::get('/lista', [CanchaController::class, 'lista'])->name('admin.canchas.lista');
        Route::get('/obtener/{id}', [CanchaController::class, 'obtener'])->name('admin.canchas.obtener');
        Route::post('/guardar', [CanchaController::class, 'guardar'])->name('admin.canchas.guardar');
        Route::post('/actualizar/{id}', [CanchaController::class, 'actualizar'])->name('admin.canchas.actualizar');
        Route::delete('/eliminar/{id}', [CanchaController::class, 'eliminar'])->name('admin.canchas.eliminar');
    });

    Route::prefix('admin/horarios')->group(function () {
        Route::get('/', [HorarioController::class, 'index'])->name('admin.horarios.index');
        Route::get('/lista', [HorarioController::class, 'lista'])->name('admin.horarios.lista');
        Route::get('/obtener/{id}', [HorarioController::class, 'obtener'])->name('admin.horarios.obtener');
        Route::post('/guardar', [HorarioController::class, 'guardar'])->name('admin.horarios.guardar');
        Route::put('/actualizar/{id}', [HorarioController::class, 'actualizar'])->name('admin.horarios.actualizar');
        Route::delete('/eliminar/{id}', [HorarioController::class, 'eliminar'])->name('admin.horarios.eliminar');
    });

    Route::prefix('admin/bloqueos')->group(function () {
        Route::get('/', [BloqueoController::class, 'index'])->name('admin.bloqueos.index');
        Route::get('/lista', [BloqueoController::class, 'lista'])->name('admin.bloqueos.lista');
        Route::get('/obtener/{id}', [BloqueoController::class, 'obtener'])->name('admin.bloqueos.obtener');
        Route::post('/guardar', [BloqueoController::class, 'guardar'])->name('admin.bloqueos.guardar');
        Route::put('/actualizar/{id}', [BloqueoController::class, 'actualizar'])->name('admin.bloqueos.actualizar');
        Route::delete('/eliminar/{id}', [BloqueoController::class, 'eliminar'])->name('admin.bloqueos.eliminar');
    });

    // Admin / Dueño — Reservas
    Route::prefix('admin/reservas')->group(function () {
        Route::get('/', [AdminReservaController::class, 'index'])->name('admin.reservas.index');
        Route::get('/lista', [AdminReservaController::class, 'lista'])->name('admin.reservas.lista');
        Route::get('/obtener/{id}', [AdminReservaController::class, 'obtener'])->name('admin.reservas.obtener');
        Route::put('/cancelar/{id}', [AdminReservaController::class, 'cancelar'])->name('admin.reservas.cancelar');
    });

    // Admin / Dueño — Historial de pagos
    Route::prefix('admin/pagos')->group(function () {
        Route::get('/', [PagoController::class, 'index'])->name('admin.pagos.index');
        Route::get('/lista', [PagoController::class, 'lista'])->name('admin.pagos.lista');
        Route::get('/obtener/{id}', [PagoController::class, 'obtener'])->name('admin.pagos.obtener');
        Route::get('/pdf/{id}', [PagoController::class, 'pdf'])->name('admin.pagos.pdf');
    });

    // Admin — Métodos de pago (catálogo global)
    Route::prefix('admin/metodos-pago')->group(function () {
        Route::get('/', [MetodoPagoController::class, 'index'])->name('admin.metodospago.index');
        Route::get('/lista', [MetodoPagoController::class, 'lista'])->name('admin.metodospago.lista');
        Route::post('/guardar', [MetodoPagoController::class, 'guardar'])->name('admin.metodospago.guardar');
        Route::get('/obtener/{id}', [MetodoPagoController::class, 'obtener'])->name('admin.metodospago.obtener');
        Route::put('/actualizar/{id}', [MetodoPagoController::class, 'actualizar'])->name('admin.metodospago.actualizar');
        Route::delete('/eliminar/{id}', [MetodoPagoController::class, 'eliminar'])->name('admin.metodospago.eliminar');
    });

    // Admin / Dueño — Reportes
    Route::prefix('admin/reportes')->group(function () {
        Route::prefix('reservas')->group(function () {
            Route::get('/', [ReporteReservaController::class, 'index'])->name('admin.reportes.reservas.index');
            Route::get('/lista', [ReporteReservaController::class, 'lista'])->name('admin.reportes.reservas.lista');
            Route::get('/exportar', [ReporteReservaController::class, 'exportar'])->name('admin.reportes.reservas.exportar');
        });

        Route::prefix('ingresos')->group(function () {
            Route::get('/', [ReporteIngresoController::class, 'index'])->name('admin.reportes.ingresos.index');
            Route::get('/lista', [ReporteIngresoController::class, 'lista'])->name('admin.reportes.ingresos.lista');
            Route::get('/exportar', [ReporteIngresoController::class, 'exportar'])->name('admin.reportes.ingresos.exportar');
        });
    });

    // Cliente — Niubiz
    Route::post('/cliente/niubiz/sesion', [ClienteReservaController::class, 'niubizSesion'])->name('cliente.niubiz.sesion');
    Route::get('/cliente/reservas/lista', [ClienteReservaController::class, 'lista'])->name('cliente.reservas.lista');
    Route::get('/cliente/reservas/{id}/comprobante', [ClienteReservaController::class, 'comprobantePdf'])->name('cliente.reservas.comprobante');

    // Legacy
    Route::get('/cliente/reservar', [ClienteReservaController::class, 'reservar'])->name('cliente.reservar');
    Route::get('/cliente/canchas-por-complejo/{id}', [ClienteReservaController::class, 'canchasPorComplejo'])->name('cliente.canchasPorComplejo');
    Route::get('/cliente/slots/{idCancha}/{fecha}', [ClienteReservaController::class, 'slots'])->name('cliente.slots');

    // Rutas del cliente
    Route::get('/cliente/perfil', [ClienteController::class, 'perfil'])->name('cliente.perfil');
    Route::post('/cliente/perfil', [ClienteController::class, 'actualizarPerfil'])->name('cliente.actualizar');
    Route::get('/cliente/reservas', [ClienteController::class, 'reservas'])->name('cliente.reservas');
});

// Niubiz confirmar — recibe browser POST de Niubiz (CSRF exempt en bootstrap/app.php)
Route::post('/cliente/niubiz/confirmar', [ClienteReservaController::class, 'niubizConfirmar'])
    ->middleware('auth')
    ->name('cliente.niubiz.confirmar');

require __DIR__.'/auth.php';
