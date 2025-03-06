<?php

use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ResetController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;

Route::post('/signin', RegistrationController::class);
Route::post('/password/reset/', [ResetController::class, 'resetPassword']);
Route::post('/password/change', [ResetController::class, 'changePassword']);

Route::group(
    [
        'as' => 'passport.',
        'prefix' => config('passport.path', '/oauth'),
    ],
    function () {
        Route::post('/token', [
            'uses' => AccessTokenController::class . '@issueToken',
            'as' => 'token',
            'middleware' => 'throttle',
        ]);
    }
);
