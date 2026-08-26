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
use App\Http\Controllers\InterestController;
use App\Http\Controllers\PlacePhotoController;
use App\Http\Controllers\ContentReportController;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\BusinessClaimController;
use App\Http\Controllers\MerchantPlaceController;
use App\Http\Controllers\Admin\PlaceManagementController;
use App\Http\Controllers\Admin\TaxonomyController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\Admin\FounderCatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/explorar',ExploreController::class)->name('explore');
Route::get('/rankings/{category?}',RankingController::class)->name('rankings.index');
Route::get('/lugares/{place}', [PlaceController::class, 'show'])->name('places.show');
Route::post('/lugares/{place}/resenas', [ReviewController::class, 'store'])->middleware(['auth', 'verified', 'throttle:5,1'])->name('reviews.store');
Route::post('/lugares/{place}/check-in', [CheckInController::class, 'store'])->middleware(['auth','verified','throttle:5,1'])->name('checkins.store');
Route::get('/visita/qr/{publicId}/{secret}',[QrCheckInController::class,'show'])->name('qr.show');
Route::get('/viajeros/{publicProfileId}',[PublicTravelerProfileController::class,'show'])->name('travelers.public');
Route::get('/fotos/{publicId}',[PlacePhotoController::class,'show'])->name('photos.show');
Route::post('/visita/qr/{publicId}/{secret}',[QrCheckInController::class,'confirm'])->middleware(['auth','verified','throttle:5,1'])->name('qr.confirm');
Route::post('/lugares/{place}/fotos',[PlacePhotoController::class,'store'])->middleware(['auth','verified','throttle:3,1'])->name('photos.store');
Route::post('/resenas/{review}/reportar',[ContentReportController::class,'review'])->middleware(['auth','verified','throttle:5,1'])->name('reports.reviews.store');
Route::post('/fotos/{photo}/reportar',[ContentReportController::class,'photo'])->middleware(['auth','verified','throttle:5,1'])->name('reports.photos.store');
Route::post('/lugares/{place}/reclamar',[BusinessClaimController::class,'store'])->middleware(['auth','verified','throttle:3,10'])->name('business.claims.store');

Route::middleware('guest')->group(function () {
    Route::get('/registro', [AuthController::class, 'register'])->name('register');
    Route::post('/registro', [AuthController::class, 'store']);
    Route::get('/ingresar', [AuthController::class, 'login'])->name('login');
    Route::post('/ingresar', [AuthController::class, 'authenticate']);
});

Route::prefix('administracion')->name('admin.')->middleware(['auth','verified','admin'])->group(function(){
    Route::get('/catalogo-fundador',[FounderCatalogController::class,'index'])->name('founder.index');
    Route::put('/catalogo-fundador/{place}',[FounderCatalogController::class,'update'])->name('founder.update');
    Route::get('/lugares',[PlaceManagementController::class,'index'])->name('places.index');
    Route::get('/lugares/crear',[PlaceManagementController::class,'create'])->name('places.create');
    Route::post('/lugares',[PlaceManagementController::class,'store'])->name('places.store');
    Route::get('/lugares/{place}/editar',[PlaceManagementController::class,'edit'])->name('places.edit');
    Route::put('/lugares/{place}',[PlaceManagementController::class,'update'])->name('places.update');
    Route::post('/categorias',[TaxonomyController::class,'category'])->name('categories.store');
    Route::put('/categorias/{category}',[TaxonomyController::class,'updateCategory'])->name('categories.update');
    Route::post('/departamentos',[TaxonomyController::class,'department'])->name('departments.store');
    Route::put('/departamentos/{department}',[TaxonomyController::class,'updateDepartment'])->name('departments.update');
    Route::get('/moderacion',[ModerationController::class,'index'])->name('moderation.index');
    Route::get('/moderacion/fotos/{photo}/vista',[ModerationController::class,'photoPreview'])->name('moderation.photos.preview');
    Route::put('/moderacion/fotos/{photo}',[ModerationController::class,'photo'])->name('moderation.photos.update');
    Route::put('/moderacion/visitas/{checkIn}',[ModerationController::class,'checkIn'])->name('moderation.checkins.update');
    Route::put('/moderacion/denuncias/{report}',[ModerationController::class,'report'])->name('moderation.reports.update');
    Route::get('/moderacion/comercios/{claim}/evidencia',[ModerationController::class,'claimDocument'])->name('moderation.claims.document');
    Route::put('/moderacion/comercios/{claim}',[ModerationController::class,'claim'])->name('moderation.claims.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/salir', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/verificar-correo', [AuthController::class, 'verificationNotice'])->name('verification.notice');
    Route::get('/verificar-correo/{id}/{hash}', [AuthController::class, 'verify'])->middleware('signed')->name('verification.verify');
    Route::post('/verificar-correo/reenviar', [AuthController::class, 'resend'])->middleware('throttle:6,1')->name('verification.send');
    Route::get('/mi-perfil', [AuthController::class, 'profile'])->middleware('verified')->name('profile');
    Route::get('/mis-intereses',[InterestController::class,'edit'])->middleware('verified')->name('interests.edit');
    Route::put('/mis-intereses',[InterestController::class,'update'])->middleware(['verified','throttle:10,1'])->name('interests.update');
    Route::put('/mi-perfil/publico',[PublicProfileSettingsController::class,'update'])->middleware(['verified','throttle:10,1'])->name('profile.public.update');
    Route::get('/mi-pasaporte',[PassportController::class,'show'])->middleware('verified')->name('passport.show');
    Route::get('/mi-pasaporte/logros/{achievement}',[AchievementCardController::class,'show'])->middleware('verified')->name('passport.achievements.card');
    Route::get('/mis-comercios',[MerchantPlaceController::class,'index'])->middleware('verified')->name('merchant.index');
    Route::get('/mis-comercios/{place}/editar',[MerchantPlaceController::class,'edit'])->middleware('verified')->name('merchant.places.edit');
    Route::put('/mis-comercios/{place}',[MerchantPlaceController::class,'update'])->middleware(['verified','throttle:10,1'])->name('merchant.places.update');
});

Route::view('/terminos', 'legal.terms')->name('legal.terms');
Route::view('/privacidad', 'legal.privacy')->name('legal.privacy');
