<?php

use App\Http\Controllers\DestroyCollaboratorController;
use App\Http\Controllers\ImportCsvCollaboratorController;
use App\Http\Controllers\UpdateCollaboratorController;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\Collaborator\GetCollaboratorsController;
use App\Http\Controllers\V1\Collaborator\StoreCollaboratorController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Middleware\CheckToken;

Route::group([
    'prefix' => 'v1',
    'name' => 'v1',
], function () {

    Route::post('/login', [AuthController::class, 'login'])->name('.login');

    /**
     * Authenticated routes
     */
    Route::middleware('auth:api')->group(function () {
        Route::group([
            'prefix' => 'collaborator',
            'name' => '.collaborator',
            'middleware' => [CheckToken::using(['manager'])],
        ], function () {

            Route::post('/', StoreCollaboratorController::class)
                ->name('.store');

            Route::put('/{collaborator}', UpdateCollaboratorController::class)
                ->name('.update');

            Route::delete('/{collaborator}', DestroyCollaboratorController::class)
                ->name('.destroy');

            Route::post('/import/csv', ImportCsvCollaboratorController::class)
                ->name('.import.csv');
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
