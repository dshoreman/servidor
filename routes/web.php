<?php

use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use Servidor\Http\Controllers\FallbackController;

Route::get('csrf', [CsrfCookieController::class, 'show']);

Route::fallback([FallbackController::class, 'frontend']);
