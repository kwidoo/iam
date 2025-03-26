<?php

use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Kwidoo\Mere\Mere;
use Laravel\Passport\Http\Controllers\AccessTokenController;

Route::post('/signin', RegistrationController::class);
Route::post('/password/reset/', [ResetController::class, 'resetPassword']);
Route::post('/password/change', [ResetController::class, 'changePassword']);
Route::post('/register/{organization:slug?}', RegistrationController::class);
Route::get('translations', TranslationController::class);
Mere::registerRoutes();

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

Route::group(
    ['middleware' => 'auth:api',],
    function () {
        Route::get('/user', UserController::class);
    }

);
