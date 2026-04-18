<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeatController;
use App\Http\Controllers\ColeccionController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\UsuarioController;

/*
|--------------------------------------------------------------------------
| INICIO
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('beat.index');
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.save');

/*
|--------------------------------------------------------------------------
| BEATS (PÚBLICO)
|--------------------------------------------------------------------------
*/
Route::get('/beat', [BeatController::class, 'index'])->name('beat.index');
Route::get('/beat/detail/{id}', [BeatController::class, 'detail'])->name('beat.detail');

/*
|--------------------------------------------------------------------------
| COLECCIONES (PÚBLICO)
|--------------------------------------------------------------------------
*/
Route::get('/coleccion', [ColeccionController::class, 'index'])->name('coleccion.index');
Route::get('/coleccion/detail/{id}', [ColeccionController::class, 'detail'])->name('coleccion.detail');

/*
|--------------------------------------------------------------------------
| ZONA LOGIN REQUERIDO
|--------------------------------------------------------------------------
*/
Route::middleware('requirelogin')->group(function () {

    // CARRITO
    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/add-beat', [CarritoController::class, 'addBeat'])->name('carrito.addBeat');
    Route::post('/carrito/add-coleccion', [CarritoController::class, 'addColeccion'])->name('carrito.addColeccion');
    Route::post('/carrito/update', [CarritoController::class, 'update'])->name('carrito.update');
    Route::get('/carrito/remove/{type}/{id}', [CarritoController::class, 'remove'])->name('carrito.remove');
    Route::get('/carrito/clear', [CarritoController::class, 'clear'])->name('carrito.clear');

    // CHECKOUT
    Route::post('/compra/checkout', [CompraController::class, 'checkout'])->name('compra.checkout');

    // COMPRAS
    Route::get('/compra', [CompraController::class, 'index'])->name('compra.index');
    Route::get('/compra/detail/{id}', [CompraController::class, 'detail'])->name('compra.detail');

    // FACTURAS
    Route::get('/factura', [FacturaController::class, 'index'])->name('factura.index');
    Route::get('/factura/detail/{id}', [FacturaController::class, 'detail'])->name('factura.detail');

    // BEATS CRUD
    Route::get('/beat/create', [BeatController::class, 'create'])->name('beat.create');
    Route::post('/beat/save', [BeatController::class, 'save'])->name('beat.save');
    Route::get('/beat/edit/{id}', [BeatController::class, 'edit'])->name('beat.edit');
    Route::post('/beat/update', [BeatController::class, 'update'])->name('beat.update');
    Route::get('/beat/delete/{id}', [BeatController::class, 'delete'])->name('beat.delete');

    // COLECCIONES CRUD
    Route::get('/coleccion/create', [ColeccionController::class, 'create'])->name('coleccion.create');
    Route::post('/coleccion/save', [ColeccionController::class, 'save'])->name('coleccion.save');
    Route::get('/coleccion/edit/{id}', [ColeccionController::class, 'edit'])->name('coleccion.edit');
    Route::post('/coleccion/update', [ColeccionController::class, 'update'])->name('coleccion.update');
    Route::get('/coleccion/delete/{id}', [ColeccionController::class, 'delete'])->name('coleccion.delete');

    // PERFIL
    Route::get('/perfil', [UsuarioController::class, 'profile'])->name('usuario.profile');
});

/*
|--------------------------------------------------------------------------
| ADMIN ONLY
|--------------------------------------------------------------------------
*/
Route::middleware('adminonly')->group(function () {

    Route::get('/usuario', [UsuarioController::class, 'index'])->name('usuario.index');
    Route::get('/usuario/create', [UsuarioController::class, 'create'])->name('usuario.create');
    Route::post('/usuario/save', [UsuarioController::class, 'save'])->name('usuario.save');
    Route::get('/usuario/edit/{id}', [UsuarioController::class, 'edit'])->name('usuario.edit');
    Route::post('/usuario/update', [UsuarioController::class, 'update'])->name('usuario.update');
    Route::get('/usuario/delete/{id}', [UsuarioController::class, 'delete'])->name('usuario.delete');
});
