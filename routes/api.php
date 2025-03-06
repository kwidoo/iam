<?php

use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;

Route::post('/v1/users', [RegistrationController::class, 'register']);

Route::group(
    [
        'as' => 'passport.',
        'prefix' => config('passport.path', '/v1/oauth'),
    ],
    function () {
        Route::post('/token', [
            'uses' => AccessTokenController::class . '@issueToken',
            'as' => 'token',
            'middleware' => 'throttle',
        ]);
    }
);
