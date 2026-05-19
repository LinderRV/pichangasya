<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Cliente\ClienteController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\PaginasController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\Usuario\UsuarioController;
use App\Http\Controllers\Admin\Rol\RolController;

Route::get('/', [PaginasController::class, 'inicio'])->name('home');



Route::prefix('web')
->name('web.paginas.')
->controller(PaginasController::class)
->group(function () {
    Route::get('/inicio', 'inicio')->name('inicio');
    Route::get('/canchas', 'canchas')->name('canchas');
    Route::get('/como-funciona', 'comoFunciona')->name('como-funciona');
    Route::get('/contacto', 'contacto')->name('contacto');
});




Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {

    Route::controller(UsuarioController::class)->group(function () {
        Route::prefix('admin')->group(function () {
            Route::prefix('usuarios')->group(function () {
                Route::get('/', [UsuarioController::class, 'index'])->name('admin.usuarios.index');
            });
            
        });
        
    });

    Route::controller(RolController::class)->group(function () {
        Route::prefix('admin')->group(function () {
            Route::prefix('rol')->group(function () {
                Route::get('/', [RolController::class, 'index'])->name('admin.rol.index');
                Route::get('/lista', [RolController::class, 'listaRol'])->name('admin.rol.lista');
            });
            
        });
        
    });




    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas del cliente
    Route::get('/cliente/perfil', [ClienteController::class, 'perfil'])->name('cliente.perfil');
    Route::post('/cliente/perfil', [ClienteController::class, 'actualizarPerfil'])->name('cliente.actualizar');
});

require __DIR__.'/auth.php';
