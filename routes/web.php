<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Cliente\ClienteController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\PaginasController;

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




Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas del cliente
    Route::get('/cliente/perfil', [ClienteController::class, 'perfil'])->name('cliente.perfil');
    Route::post('/cliente/perfil', [ClienteController::class, 'actualizarPerfil'])->name('cliente.actualizar');
});

require __DIR__.'/auth.php';
