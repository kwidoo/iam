<?php

// use App\Http\Controllers\AuthController;

use App\Http\Controllers\Auth\AccessTokenController;
use App\Http\Controllers\Auth\HeartbeatController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserReadController;
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

Route::get('/emails/confirm', [EmailController::class, 'confirm'])
    ->name('verification.verify')->middleware('signed:relative');


Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/heartbeat', HeartbeatController::class);

    Route::resource('users', UserReadController::class)->only(['index', 'show']);




    Route::resource('emails', EmailController::class)->only(['store', 'destroy']);
    Route::patch('/emails/{email}/primary', [EmailController::class, 'setPrimary']);
    Route::post('/emails/resend', [EmailController::class, 'resend']);

    Route::resource('profiles', ProfileController::class)->only(['store', 'update', 'destroy']);
});
