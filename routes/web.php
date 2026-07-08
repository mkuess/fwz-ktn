<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// TODO: Org-Admin frontend login at /org/login (separate guard)
// TODO: Member frontend login at /profil/login (separate guard)
// These will be implemented when building the public frontend
