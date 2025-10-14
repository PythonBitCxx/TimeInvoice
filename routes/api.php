<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;

//Route::middleware('auth:sanctum')->group(function () {

Route::get('/user', function () {
    return request()->user();
});

//Route::apiResource('clients', ClientController::class);

Route::get('clients', [ClientController::class, 'index']);
Route::post('clients', [ClientController::class, 'store']);
Route::get('clients/{client}', [ClientController::class, 'show']);
Route::put('clients/{client}', [ClientController::class, 'update']);
Route::delete('clients/{client}', [ClientController::class, 'destroy']);
//});
