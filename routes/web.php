<?php

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
| Route umum (landing, profil, autentikasi). Route bisnis GISTA berada di
| routes/backend.php dan di-include di bagian bawah file ini.
*/

use Illuminate\Support\Facades\Route;

// Redirect root to dashboard/payment or login
Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));

Route::middleware(['auth'])->group(function () {
    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/backend.php';
require __DIR__.'/auth.php';
