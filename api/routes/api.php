<?php

use App\Http\Controllers\UpdateCollaboratorController;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\Collaborator\GetCollaboratorsController;
use App\Http\Controllers\V1\Collaborator\StoreCollaboratorController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Middleware\CheckToken;

Route::group(['prefix' => 'v1', 'name' => 'v1'], function () {

    Route::post('/login', [AuthController::class, 'login'])->name('.login');

    /**
     * Authenticated routes
     */
    Route::middleware('auth:passport')->group(function () {
        Route::group(['prefix' => 'collaborator', 'name' => '.collaborator'], function () {

            Route::post('/', StoreCollaboratorController::class)
                ->middleware([CheckToken::using(['manager'])])
                ->name('.collaborator.store');

            Route::put('/{collaborator}', UpdateCollaboratorController::class)
                ->middleware([CheckToken::using(['manager'])])
                ->name('.collaborator.update');
        });

        Route::get('/collaborators', GetCollaboratorsController::class)
            ->middleware([CheckToken::using(['manager'])])
            ->name('.collaborators');

        Route::post('/logout', [AuthController::class, 'logout'])->name('.logout');

        Route::get('/me', [AuthController::class, 'me'])
            ->middleware(CheckToken::using(['manager']))
            ->name('.me');
    });
});
