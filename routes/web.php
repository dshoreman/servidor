<?php

use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use Servidor\Http\Controllers\Auth\Login;
use Servidor\Http\Controllers\Auth\Register;
use Servidor\Http\Controllers\FallbackController;

Route::get('csrf', [CsrfCookieController::class, 'show']);
Route::post('register', Register::class);
Route::post('login', Login::class);

Route::fallback([FallbackController::class, 'frontend']);
