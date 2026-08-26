<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/registro', [AuthController::class, 'register'])->name('register');
    Route::post('/registro', [AuthController::class, 'store']);
    Route::get('/ingresar', [AuthController::class, 'login'])->name('login');
    Route::post('/ingresar', [AuthController::class, 'authenticate']);
});

Route::middleware('auth')->group(function () {
    Route::post('/salir', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/verificar-correo', [AuthController::class, 'verificationNotice'])->name('verification.notice');
    Route::get('/verificar-correo/{id}/{hash}', [AuthController::class, 'verify'])->middleware('signed')->name('verification.verify');
    Route::post('/verificar-correo/reenviar', [AuthController::class, 'resend'])->middleware('throttle:6,1')->name('verification.send');
    Route::get('/mi-perfil', [AuthController::class, 'profile'])->middleware('verified')->name('profile');
});

Route::view('/terminos', 'legal.terms')->name('legal.terms');
Route::view('/privacidad', 'legal.privacy')->name('legal.privacy');
