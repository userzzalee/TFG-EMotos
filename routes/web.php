<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MerchandisingController;
use App\Http\Controllers\TallerController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/merchandising', [MerchandisingController::class, 'index'])->name('merchandising');
Route::get('/admin/productos/crear', [MerchandisingController::class, 'create'])->name('merchandising.create');
Route::post('/admin/productos', [MerchandisingController::class, 'store'])->name('merchandising.store');

// ── Carrito ─────────────────────────────────────────────────────────────
Route::prefix('carrito')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update/{id}', [CartController::class, 'update'])->name('update');
    Route::post('/remove/{id}', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/perfil', function () {
    return view('perfil');
})->middleware('auth')->name('perfil');

Route::middleware('auth')->get('/taller', [App\Http\Controllers\TallerController::class, 'index'])->name('taller');

Route::get('/configurador', function () {
    return view('configurador');
})->name('configurador');

// ── Taller – Usuario ──────────────────────────────────────────────────────────
Route::middleware('auth')->prefix('taller')->name('taller.')->group(function () {
    Route::get('/nueva',             [TallerController::class, 'crear'])    ->name('crear');
    Route::post('/nueva',            [TallerController::class, 'guardar'])   ->name('guardar');
    Route::get('/mis-citas',         [TallerController::class, 'misCitas'])  ->name('mis-citas');
    Route::post('/{cita}/pagar',     [TallerController::class, 'pagar'])     ->name('pagar');
});

// ── Taller – Mecánico ─────────────────────────────────────────────────────────
Route::middleware('auth')->prefix('taller/mecanico')->name('taller.')->group(function () {
    Route::get('/nuevas',            [TallerController::class, 'nuevasCitas'])     ->name('nuevas-citas');
    Route::post('/{cita}/aceptar',   [TallerController::class, 'aceptar'])         ->name('aceptar');
    Route::get('/pendiente',         [TallerController::class, 'trabajoPendiente'])->name('trabajo-pendiente');
    Route::get('/historial',         [TallerController::class, 'historial'])       ->name('historial');
    Route::get('/{cita}',            [TallerController::class, 'detalleCita'])     ->name('detalle-cita');
    Route::post('/{cita}/comentar',  [TallerController::class, 'comentar'])        ->name('comentar');
    Route::post('/{cita}/finalizar', [TallerController::class, 'finalizar'])       ->name('finalizar');
});

require __DIR__.'/auth.php';
