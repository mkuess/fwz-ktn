<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\BenefitController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemberRegistrationController;
use App\Http\Controllers\OrganisationRegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/impressum', [HomeController::class, 'impressum'])->name('impressum');
Route::get('/datenschutz', [HomeController::class, 'datenschutz'])->name('datenschutz');
Route::get('/barrierefreiheit', [HomeController::class, 'barrierefreiheit'])->name('barrierefreiheit');
Route::get('/in-arbeit', [HomeController::class, 'inArbeit'])->name('in-arbeit');

Route::get('/benefits', [BenefitController::class, 'index'])->name('benefits.index');

Route::get('/aktuelles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/aktuelles/{slug}', [ArticleController::class, 'show'])->name('articles.show');

Route::get('/vereine/suche', [HomeController::class, 'vereineSuche'])->name('vereine.suche');

Route::prefix('registrieren')->name('registrierung.')->group(function () {
    Route::get('/',         [OrganisationRegistrationController::class, 'schritt1'])->name('schritt1');
    Route::post('/',        [OrganisationRegistrationController::class, 'schritt1Post'])->name('schritt1.post');
    Route::get('/schritt2', [OrganisationRegistrationController::class, 'schritt2'])->name('schritt2');
    Route::post('/schritt2',[OrganisationRegistrationController::class, 'schritt2Post'])->name('schritt2.post');
    Route::get('/schritt3', [OrganisationRegistrationController::class, 'schritt3'])->name('schritt3');
    Route::post('/schritt3',[OrganisationRegistrationController::class, 'schritt3Post'])->name('schritt3.post');
    Route::get('/danke',    [OrganisationRegistrationController::class, 'danke'])->name('danke');
});

Route::get('/mitglied-werden',       [MemberRegistrationController::class, 'show'])->name('member.register');
Route::post('/mitglied-werden',      [MemberRegistrationController::class, 'store'])->name('member.register.store');
Route::get('/mitglied-werden/danke', [MemberRegistrationController::class, 'danke'])->name('member.register.danke');

// TODO: Org-Admin frontend login at /org/login (separate guard)
// TODO: Member frontend login at /profil/login (separate guard)
