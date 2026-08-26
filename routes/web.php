<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\QrCheckInController;
use App\Http\Controllers\PassportController;
use App\Http\Controllers\AchievementCardController;
use App\Http\Controllers\PublicTravelerProfileController;
use App\Http\Controllers\PublicProfileSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/lugares/{place}', [PlaceController::class, 'show'])->name('places.show');
Route::post('/lugares/{place}/resenas', [ReviewController::class, 'store'])->middleware(['auth', 'verified', 'throttle:5,1'])->name('reviews.store');
Route::post('/lugares/{place}/check-in', [CheckInController::class, 'store'])->middleware(['auth','verified','throttle:5,1'])->name('checkins.store');
Route::get('/visita/qr/{publicId}/{secret}',[QrCheckInController::class,'show'])->name('qr.show');
Route::get('/viajeros/{publicProfileId}',[PublicTravelerProfileController::class,'show'])->name('travelers.public');
Route::post('/visita/qr/{publicId}/{secret}',[QrCheckInController::class,'confirm'])->middleware(['auth','verified','throttle:5,1'])->name('qr.confirm');

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
    Route::put('/mi-perfil/publico',[PublicProfileSettingsController::class,'update'])->middleware(['verified','throttle:10,1'])->name('profile.public.update');
    Route::get('/mi-pasaporte',[PassportController::class,'show'])->middleware('verified')->name('passport.show');
    Route::get('/mi-pasaporte/logros/{achievement}',[AchievementCardController::class,'show'])->middleware('verified')->name('passport.achievements.card');
});

Route::view('/terminos', 'legal.terms')->name('legal.terms');
Route::view('/privacidad', 'legal.privacy')->name('legal.privacy');
