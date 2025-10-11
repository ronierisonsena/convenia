<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\Collaborator\StoreCollaboratorController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Middleware\CheckToken;

Route::group(['prefix' => 'v1', 'name' => 'v1'], function () {

    Route::post('/login', [AuthController::class, 'login'])->name('.login');

    /**
     * Authenticated routes
     */
    Route::middleware('auth:passport')->group(function () {
        Route::post('/collaborator', StoreCollaboratorController::class)
            ->middleware([CheckToken::using(['manager'])])
            ->name('.collaborator.store');

        Route::get('/user', [AuthController::class, 'me'])
            ->middleware(CheckToken::using(['manager']))
            ->name('.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('.logout');
    });
});
