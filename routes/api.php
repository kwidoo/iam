<?php

use App\Http\Controllers\Auth\HeartbeatController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserReadController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;

Route::group(
    [
        'as' => 'passport.',
        'prefix' => config('passport.path', 'oauth'),
    ],
    function () {
        Route::post('/token', [
            'uses' => AccessTokenController::class . '@issueToken',
            'as' => 'token',
            'middleware' => 'throttle',
        ]);
    }
);

/** register */
Route::resource('users', UserController::class)->only(['store', 'update', 'destroy']);

Route::get('/emails/confirm', [EmailController::class, 'confirm'])
    ->name('verification.verify')->middleware('signed:relative');

/** direct frontend connection to IAM server */
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('user', UserReadController::class);

    Route::get('/heartbeat', HeartbeatController::class);

    Route::resource('emails', EmailController::class)->only(['store', 'destroy']);
    Route::patch('/emails/{email}/primary', [EmailController::class, 'setPrimary']);
    Route::post('/emails/resend', [EmailController::class, 'resend']);

    Route::resource('organizations', OrganizationController::class)->middleware('can:viewAny,App\Models\Organization');
});

/** services connection to IAM server */
Route::group(['prefix' => 'e', 'middleware' => ['client']], function () {
    Route::group(['middleware' => ['auth:iam']], function () {
        Route::get('user', UserReadController::class);
    });
    Route::post('/service/register', [ServiceController::class, 'store']);
});
