<?php

use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use Servidor\Http\Controllers\Auth\LoginWithEmail;
use Servidor\Http\Controllers\FallbackController;

Route::get('csrf', [CsrfCookieController::class, 'show']);
Route::post('login', LoginWithEmail::class);

Route::fallback([FallbackController::class, 'frontend']);
