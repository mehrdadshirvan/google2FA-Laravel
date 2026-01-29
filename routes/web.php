<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified','google2FA'])
    ->name('dashboard');

Route::get('/verify', [\App\Http\Controllers\Admin\Google2FAController::class, 'create'])->name('2fa.verify');
Route::post('/active', [\App\Http\Controllers\Admin\Google2FAController::class, 'active2FA'])->name('2fa.active');


Route::middleware(['auth','google2FA'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/enable', [\App\Http\Controllers\Admin\Google2FAController::class, 'index'])->name('2fa.index');
    Route::post('/confirm', [\App\Http\Controllers\Admin\Google2FAController::class, 'store'])->name('2fa.store');

});

require __DIR__.'/auth.php';
