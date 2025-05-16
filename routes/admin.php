<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin API routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// User Management Routes
Route::middleware(['auth:api', 'role:admin|super-admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // Role management for users
    Route::get('/roles', [UserController::class, 'roles']);
    Route::post('/users/{id}/roles', [UserController::class, 'assignRoles']);
    Route::delete('/users/{id}/roles', [UserController::class, 'removeRoles']);
});

// OAuth Client Management Routes
Route::middleware(['auth:api', 'role:admin|super-admin'])->group(function () {
    Route::get('/clients', [ClientController::class, 'index']);
    Route::post('/clients', [ClientController::class, 'store']);
    Route::get('/clients/{id}', [ClientController::class, 'show']);
    Route::put('/clients/{id}', [ClientController::class, 'update']);
    Route::delete('/clients/{id}', [ClientController::class, 'destroy']);

    // Client secret rotation
    Route::post('/clients/{id}/rotate-secret', [ClientController::class, 'rotateSecret']);
});
