<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MerchandisingController;
use App\Http\Controllers\TallerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\SegundaManoController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/merchandising', [MerchandisingController::class, 'index'])->name('merchandising');

// Gestión de productos: solo administradores autenticados.
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/productos/crear', [MerchandisingController::class, 'create'])->name('merchandising.create');
    Route::post('/admin/productos', [MerchandisingController::class, 'store'])->name('merchandising.store');
    Route::get('/admin/productos/{id}/editar', [MerchandisingController::class, 'edit'])->name('merchandising.edit');
    Route::post('/admin/productos/{id}', [MerchandisingController::class, 'update'])->name('merchandising.update');
});

// Carrito
Route::prefix('carrito')->name('cart.')->group(function () {
    Route::get('/',              [CartController::class, 'index'])            ->name('index');
    Route::post('/add',          [CartController::class, 'add'])              ->name('add');
    Route::post('/add-config',   [CartController::class, 'addConfiguration']) ->name('add-config');
    Route::post('/update/{id}',  [CartController::class, 'update'])           ->name('update');
    Route::post('/remove/{id}',  [CartController::class, 'remove'])           ->name('remove');
    Route::post('/clear',        [CartController::class, 'clear'])            ->name('clear');
});

// Pedidos
Route::middleware('auth')->prefix('pedidos')->name('order.')->group(function () {
    Route::get('/checkout', [App\Http\Controllers\OrderController::class, 'checkout'])->name('checkout');
    Route::post('/', [App\Http\Controllers\OrderController::class, 'store'])->name('store');
    Route::get('/', [App\Http\Controllers\OrderController::class, 'index'])->name('index');
    Route::get('/{id}', [App\Http\Controllers\OrderController::class, 'show'])->name('show');
});

// Segunda Mano
Route::prefix('segunda-mano')->name('segundamano.')->group(function () {
    // Públicas
    Route::get('/', [SegundaManoController::class, 'index'])->name('index');

    // Requieren login
    Route::middleware('auth')->group(function () {
        Route::get('/mis-anuncios',              [SegundaManoController::class, 'misAnuncios'])->name('mis-anuncios');
        Route::get('/anuncio/crear',             [SegundaManoController::class, 'create'])->name('crear');
        Route::post('/anuncio/crear',            [SegundaManoController::class, 'store'])->name('store');
        Route::get('/anuncio/{anuncio}/editar',  [SegundaManoController::class, 'edit'])->name('editar');
        Route::post('/anuncio/{anuncio}/editar', [SegundaManoController::class, 'update'])->name('update');
        Route::post('/anuncio/{anuncio}/borrar', [SegundaManoController::class, 'destroy'])->name('destroy');
        Route::post('/{anuncio}/contactar',      [SegundaManoController::class, 'contactar'])->name('contactar');
    });

    // Esta va AL FINAL para no capturar las rutas específicas
    Route::get('/{anuncio}', [SegundaManoController::class, 'show'])->name('show');
});
// Chat
Route::middleware('auth')->prefix('chat')->name('chat.')->group(function () {
    Route::get('/',                         [ChatController::class, 'index'])           ->name('index');
    Route::get('/no-leidos/contador',       [ChatController::class, 'contadorNoLeidos'])->name('contador');
    Route::post('/iniciar/{producto}',      [ChatController::class, 'iniciar'])         ->name('iniciar');
    Route::get('/{conversacion}',           [ChatController::class, 'show'])            ->name('show');
    Route::post('/{conversacion}/mensajes', [ChatController::class, 'enviar'])          ->name('enviar');
    Route::post('/{conversacion}/leer',     [ChatController::class, 'leer'])            ->name('leer');
});

// Notificaciones
Route::middleware('auth')->prefix('notificaciones')->name('notificaciones.')->group(function () {
    Route::get('/',              [NotificacionController::class, 'index'])     ->name('index');
    Route::get('/recientes',     [NotificacionController::class, 'recientes']) ->name('recientes');
    Route::post('/leer-todas',   [NotificacionController::class, 'leerTodas']) ->name('leer-todas');
    Route::get('/{id}',          [NotificacionController::class, 'abrir'])     ->name('abrir');
});

// Perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])   ->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update']) ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/perfil', function () {
    return view('perfil');
})->middleware('auth')->name('perfil');

Route::middleware('auth')->get('/taller', [TallerController::class, 'index'])->name('taller');

Route::get('/configurador', function () {
    return view('configurador');
})->name('configurador');

// Taller Usuario
Route::middleware('auth')->prefix('taller')->name('taller.')->group(function () {
    Route::get('/nueva',         [TallerController::class, 'crear'])   ->name('crear');
    Route::post('/nueva',        [TallerController::class, 'guardar']) ->name('guardar');
    Route::get('/mis-citas',     [TallerController::class, 'misCitas'])->name('mis-citas');
    Route::post('/{cita}/pagar', [TallerController::class, 'pagar'])   ->name('pagar');
    Route::get('/{cita}/pago/exito',     [TallerController::class, 'pagoExito'])    ->name('pago.exito');
    Route::get('/{cita}/pago/cancelado', [TallerController::class, 'pagoCancelado'])->name('pago.cancelado');
});

// Taller Mecánico
Route::middleware('auth')->prefix('taller/mecanico')->name('taller.')->group(function () {
    Route::get('/nuevas',            [TallerController::class, 'nuevasCitas'])     ->name('nuevas-citas');
    Route::post('/{cita}/aceptar',   [TallerController::class, 'aceptar'])         ->name('aceptar');
    Route::get('/pendiente',         [TallerController::class, 'trabajoPendiente'])->name('trabajo-pendiente');
    Route::get('/historial',         [TallerController::class, 'historial'])       ->name('historial');
    Route::get('/{cita}',            [TallerController::class, 'detalleCita'])     ->name('detalle-cita');
    Route::post('/{cita}/comentar',  [TallerController::class, 'comentar'])        ->name('comentar');
    Route::post('/{cita}/finalizar', [TallerController::class, 'finalizar'])       ->name('finalizar');
});

// Panel Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/usuarios', [AdminController::class, 'index'])->name('usuarios');
    Route::patch('/usuarios/{usuario}/rol', [AdminController::class, 'actualizarRol'])->name('usuarios.rol');
    Route::delete('/usuarios/{usuario}', [AdminController::class, 'eliminar'])->name('usuarios.eliminar');
});

require __DIR__.'/auth.php';
