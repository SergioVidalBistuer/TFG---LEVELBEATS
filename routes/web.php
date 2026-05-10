<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BeatController;
use App\Http\Controllers\StudioBeatController;
use App\Http\Controllers\StudioColeccionController;
use App\Http\Controllers\StudioServicioController;
use App\Http\Controllers\StudioProyectoController;
use App\Http\Controllers\UsuarioEncargoController;
use App\Http\Controllers\MensajeProyectoController;
use App\Http\Controllers\ArchivoProyectoController;
use App\Http\Controllers\ColeccionController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AdminAuditoriaController;
use App\Http\Controllers\AdminServicioController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\GuardadoController;

/*
|--------------------------------------------------------------------------
| INICIO
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/buscar', [SearchController::class, 'index'])->name('search.index');

Route::view('/aviso-legal', 'legal.aviso-legal')->name('legal.aviso');
Route::view('/politica-privacidad', 'legal.politica-privacidad')->name('legal.privacidad');
Route::view('/politica-cookies', 'legal.politica-cookies')->name('legal.cookies');

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
| SERVICIOS (PÚBLICO)
|--------------------------------------------------------------------------
*/
Route::get('/servicios', [ServicioController::class, 'index'])->name('servicio.index');
Route::get('/servicios/detail/{id}', [ServicioController::class, 'detail'])->name('servicio.detail');
Route::get('/usuario/perfil/{id}', [UsuarioController::class, 'publicProfile'])->name('usuario.profile.public');

/*
|--------------------------------------------------------------------------
| ZONA LOGIN REQUERIDO
|--------------------------------------------------------------------------
*/
Route::middleware('requirelogin')->group(function () {

    // ONBOARDING (Post-Registro)
    Route::get('/onboarding/roles', [OnboardingController::class, 'showRoles'])->name('onboarding.roles');
    Route::post('/onboarding/roles', [OnboardingController::class, 'setRole'])->name('onboarding.setRole');
    Route::get('/onboarding/planes/{rol}', [OnboardingController::class, 'showPlanes'])->name('onboarding.planes');
    Route::post('/onboarding/subscribe', [OnboardingController::class, 'subscribe'])->name('onboarding.subscribe');

    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/add-beat', [CarritoController::class, 'addBeat'])->name('carrito.addBeat');
    Route::post('/carrito/add-coleccion', [CarritoController::class, 'addColeccion'])->name('carrito.addColeccion');
    Route::get('/carrito/remove/{type}/{id}', [CarritoController::class, 'remove'])->name('carrito.remove');
    Route::get('/carrito/clear', [CarritoController::class, 'clear'])->name('carrito.clear');

    // CONTACTO A INGENIERO (desde página de servicio)
    Route::post('/servicios/{id}/contacto', [ServicioController::class, 'contacto'])->name('servicio.contacto');

    // CHECKOUT
    Route::get('/compra/checkout', [CompraController::class, 'showCheckout'])->name('compra.checkout.show');
    Route::post('/compra/checkout/process', [CompraController::class, 'processCheckout'])->name('compra.checkout.process');

    // COMPRAS
    Route::get('/compra', [CompraController::class, 'index'])->name('compra.index');
    Route::get('/compra/detail/{id}', [CompraController::class, 'detail'])->name('compra.detail');

    // FACTURACIÓN Y COMPRAS (CLIENTE)
    Route::get('/usuario/facturacion', [FacturaController::class, 'index'])->name('usuario.facturacion.index');
    Route::get('/usuario/facturacion/detail/{id}', [FacturaController::class, 'detail'])->name('usuario.facturacion.detail');

    // PROYECTOS / ENCARGOS CONTRATADOS (CLIENTE)
    Route::get('/usuario/encargos', [UsuarioEncargoController::class, 'index'])->name('usuario.encargos.index');
    Route::get('/usuario/encargos/detail/{id}', [UsuarioEncargoController::class, 'detail'])->name('usuario.encargos.detail');

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

    // MENSAJERÍA (HILO DE PROYECTO UNIVERSAL B2B)
    Route::post('/mensajes/proyecto/{id}', [MensajeProyectoController::class, 'enviar'])->name('mensajes.proyecto.enviar');

    // ARCHIVOS DE PROYECTO (SUBIDA UNIVERSAL B2B)
    Route::post('/proyectos/{id}/archivos', [ArchivoProyectoController::class, 'upload'])->name('proyectos.archivos.upload');
    Route::get('/proyectos/{id}/archivos/descargar', [ArchivoProyectoController::class, 'download'])->name('proyectos.archivos.download');
    Route::post('/proyectos/{proyecto}/aceptar-ingeniero', [UsuarioEncargoController::class, 'aceptarIngeniero'])->name('proyectos.aceptarIngeniero');
    Route::post('/proyectos/{proyecto}/aceptar-pagar', [UsuarioEncargoController::class, 'aceptarPagar'])->name('proyectos.aceptarPagar');
    Route::post('/proyectos/{proyecto}/cancelar-servicio', [UsuarioEncargoController::class, 'cancelarServicio'])->name('proyectos.cancelarServicio');

    // PERFIL
    Route::get('/perfil', [UsuarioController::class, 'profile'])->name('usuario.profile');
    Route::get('/usuario/ajustes', [UsuarioController::class, 'settings'])->name('usuario.settings');
    Route::post('/usuario/ajustes/perfil', [UsuarioController::class, 'updateSettingsProfile'])->name('usuario.settings.profile');
    Route::post('/usuario/ajustes/foto', [UsuarioController::class, 'updateSettingsPhoto'])->name('usuario.settings.photo');
    Route::post('/usuario/ajustes/password', [UsuarioController::class, 'updateSettingsPassword'])->name('usuario.settings.password');
    Route::get('/usuario/mis-productos', [UsuarioController::class, 'misProductos'])->name('usuario.productos.index');
    Route::get('/usuario/mis-productos/beats/{id}/descargar', [UsuarioController::class, 'descargarBeatComprado'])->name('usuario.productos.beats.descargar');
    Route::get('/usuario/mis-productos/licencia/{detalleCompra}/ver', [UsuarioController::class, 'verLicenciaComprada'])->name('usuario.productos.licencia.ver');
    Route::get('/usuario/mis-productos/licencia/{detalleCompra}/descargar', [UsuarioController::class, 'descargarLicenciaComprada'])->name('usuario.productos.licencia.descargar');

    // GUARDADOS
    Route::get('/usuario/guardados', [GuardadoController::class, 'index'])->name('usuario.guardados.index');
    Route::post('/guardados/toggle', [GuardadoController::class, 'toggle'])->name('guardados.toggle');
    Route::post('/guardados/{tipo}/{id}/eliminar', [GuardadoController::class, 'eliminar'])->name('guardados.eliminar');

    // === STUDIO PANEL (VENDEDOR/INGENIERO) ===
    Route::prefix('studio')->name('studio.')->group(function () {
        // BEATS (PRODUCTOR)
        Route::prefix('beats')->name('beats.')->group(function () {
            Route::get('/', [StudioBeatController::class, 'index'])->name('index');
            Route::get('/create', [StudioBeatController::class, 'create'])->name('create');
            Route::post('/save', [StudioBeatController::class, 'save'])->name('save');
            Route::get('/edit/{id}', [StudioBeatController::class, 'edit'])->name('edit');
            Route::post('/update', [StudioBeatController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [StudioBeatController::class, 'delete'])->name('delete');
        });

        // COLECCIONES (PRODUCTOR)
        Route::prefix('colecciones')->name('colecciones.')->group(function () {
            Route::get('/', [StudioColeccionController::class, 'index'])->name('index');
            Route::get('/create', [StudioColeccionController::class, 'create'])->name('create');
            Route::post('/save', [StudioColeccionController::class, 'save'])->name('save');
            Route::get('/edit/{id}', [StudioColeccionController::class, 'edit'])->name('edit');
            Route::post('/update', [StudioColeccionController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [StudioColeccionController::class, 'delete'])->name('delete');
        });

        // SERVICIOS (INGENIERO/B2B)
        Route::prefix('servicios')->name('servicios.')->group(function () {
            Route::get('/', [StudioServicioController::class, 'index'])->name('index');
            Route::get('/create', [StudioServicioController::class, 'create'])->name('create');
            Route::post('/save', [StudioServicioController::class, 'save'])->name('save');
            Route::get('/edit/{id}', [StudioServicioController::class, 'edit'])->name('edit');
            Route::post('/update', [StudioServicioController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [StudioServicioController::class, 'delete'])->name('delete');
        });

        // PROYECTOS (ESTUDIO INGENIERO / TASK MANAGER)
        Route::prefix('proyectos')->name('proyectos.')->group(function () {
            Route::get('/', [StudioProyectoController::class, 'index'])->name('index');
            Route::get('/edit/{id}', [StudioProyectoController::class, 'edit'])->name('edit');
            Route::post('/update', [StudioProyectoController::class, 'update'])->name('update');
            Route::delete('/{proyecto}', [StudioProyectoController::class, 'destroy'])->name('destroy');
        });
    });
});

/*
|--------------------------------------------------------------------------
| ADMIN ONLY
|--------------------------------------------------------------------------
*/
Route::middleware('adminonly')->group(function () {
    
    // DASHBOARD PRINCIPAL
    Route::get('/admin/dashboard', [\App\Http\Controllers\AdminDashboardController::class, 'index'])->name('admin.dashboard.index');

    // AUDITORIA (ROOT)
    Route::get('/admin/auditoria', [AdminAuditoriaController::class, 'index'])->name('admin.auditoria.index');

    Route::get('/usuario', [UsuarioController::class, 'index'])->name('usuario.index');
    Route::get('/usuario/create', [UsuarioController::class, 'create'])->name('usuario.create');
    Route::post('/usuario/save', [UsuarioController::class, 'save'])->name('usuario.save');
    Route::get('/usuario/edit/{id}', [UsuarioController::class, 'edit'])->name('usuario.edit');
    Route::post('/usuario/update', [UsuarioController::class, 'update'])->name('usuario.update');
    Route::get('/usuario/delete/{id}', [UsuarioController::class, 'delete'])->name('usuario.delete');

    // SERVICIOS B2B (ADMIN — CRUD GLOBAL)
    Route::prefix('admin/servicios')->name('admin.servicios.')->group(function () {
        Route::get('/', [AdminServicioController::class, 'index'])->name('index');
        Route::get('/create', [AdminServicioController::class, 'create'])->name('create');
        Route::post('/save', [AdminServicioController::class, 'save'])->name('save');
        Route::get('/edit/{id}', [AdminServicioController::class, 'edit'])->name('edit');
        Route::post('/update', [AdminServicioController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [AdminServicioController::class, 'delete'])->name('delete');
    });
});
