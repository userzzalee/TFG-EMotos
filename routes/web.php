<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MerchandisingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/merchandising', [MerchandisingController::class, 'index'])->name('merchandising');
Route::get('/admin/productos/crear', [MerchandisingController::class, 'create'])->name('merchandising.create');
Route::post('/admin/productos', [MerchandisingController::class, 'store'])->name('merchandising.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/perfil', function () {
    return view('perfil');
})->middleware('auth')->name('perfil');

Route::get('/configurador', function () {
    return view('configurador');
})->name('configurador');


require __DIR__.'/auth.php';
