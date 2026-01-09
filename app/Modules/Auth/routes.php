<?php

use App\Modules\Auth\Controllers\EmailVerificationNotificationController;
use App\Modules\Auth\Controllers\NewPasswordController;
use App\Modules\Auth\Controllers\AuthenticatedSessionController;
use App\Modules\Auth\Controllers\PasswordResetLinkController;
use App\Modules\Auth\Controllers\RegisteredUserController;
use App\Modules\Auth\Controllers\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {

    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->name('register');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->name('login');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest')
        ->name('password.email');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('guest')
        ->name('password.store');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');

        Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['auth:sanctum', 'signed'])
            ->name('verification.verify');
    });
});
