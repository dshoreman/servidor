<?php

use Illuminate\Support\Facades\Route;
use Servidor\Http\Controllers\Auth\ForgotPasswordController;
use Servidor\Http\Controllers\Auth\LoginWithEmail;
use Servidor\Http\Controllers\Auth\ResetPasswordController;
use Servidor\Http\Controllers\Auth\VerificationController;
use Servidor\Http\Controllers\FallbackController;

Route::post('login', LoginWithEmail::class);

// Auth routes copied from Illuminate\Routing\Router@auth
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::get('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

Route::fallback([FallbackController::class, 'frontend']);
