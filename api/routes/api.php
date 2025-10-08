<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function (Request $request) {
    return 'ok';
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
