<?php

// use App\Http\Controllers\AuthController;

use App\Http\Controllers\AccessTokenController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::group([
    'as' => 'passport.',
    'prefix' => config('passport.path', 'oauth'),
], function () {
    Route::post('/token', [
        'uses' => AccessTokenController::class . '@issueToken',
        'as' => 'token',
        'middleware' => 'throttle',
    ]);
});


Route::resource('users', UserController::class)->only(['store', 'update', 'destroy']);
Route::get('/users/heartbeat', [UserController::class, 'heartBeat']);
Route::resource('emails', EmailController::class)->only(['store', 'update', 'destroy'])->middleware('auth:api');
Route::get('/emails/confirm', [EmailController::class, 'confirm'])->name('verification.verify')->middleware(['signed', 'auth:api']);
Route::patch('/emails/{email}/primary', [EmailController::class, 'setPrimary'])->middleware(['auth:api']);
