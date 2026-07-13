<?php

/**
 * In routes/web.php ergänzen (zusätzlich zu den bereits vorhandenen Routen
 * aus dem Rollback: home, impressum, datenschutz, barrierefreiheit, in-arbeit).
 */

use App\Http\Controllers\OrganisationRegistrationController;
use Illuminate\Support\Facades\Route;

Route::prefix('registrieren')->name('registrierung.')->group(function () {
    Route::get('/', [OrganisationRegistrationController::class, 'schritt1'])->name('schritt1');
    Route::post('/', [OrganisationRegistrationController::class, 'schritt1Speichern'])->name('schritt1.speichern');

    Route::get('/konto', [OrganisationRegistrationController::class, 'schritt2'])->name('schritt2');
    Route::post('/konto', [OrganisationRegistrationController::class, 'schritt2Speichern'])->name('schritt2.speichern');

    Route::get('/standort', [OrganisationRegistrationController::class, 'schritt3'])->name('schritt3');
    Route::post('/standort', [OrganisationRegistrationController::class, 'schritt3Speichern'])->name('schritt3.speichern');

    Route::get('/danke', [OrganisationRegistrationController::class, 'danke'])->name('danke');
});
