<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/impressum', [HomeController::class, 'impressum'])->name('impressum');
Route::get('/datenschutz', [HomeController::class, 'datenschutz'])->name('datenschutz');
Route::get('/barrierefreiheit', [HomeController::class, 'barrierefreiheit'])->name('barrierefreiheit');
Route::get('/in-arbeit', [HomeController::class, 'inArbeit'])->name('in-arbeit');

// TODO: Org-Admin frontend login at /org/login (separate guard)
// TODO: Member frontend login at /profil/login (separate guard)
