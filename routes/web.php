<?php

use App\Http\Controllers\Api\GoogleAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('oauth/google/redirect', [GoogleAuthController::class, 'redirect']);
Route::get('oauth/google/callback', [GoogleAuthController::class, 'callback']);


