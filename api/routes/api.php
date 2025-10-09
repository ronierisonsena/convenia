<?php

use App\Http\Controllers\v1\AuthController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'name' => 'v1'], function () {

    Route::post('/register', [AuthController::class, 'register'])->name('.register');
    Route::post('/login', [AuthController::class, 'login'])->name('.login');

    /**
     * Authenticated routes
     */
    Route::middleware('auth:passport')->group(function () {
        Route::get('/user', [AuthController::class, 'me'])->name('.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('.logout');
    });
});
