<?php

use App\Modules\User\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix("/users")->middleware('auth:sanctum')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/me', [UserController::class, 'me'])->name("me");
    Route::put('/{id}', [UserController::class, 'update'])->name('update');
    Route::get('/{id}', [UserController::class, 'show'])->name('show');
});