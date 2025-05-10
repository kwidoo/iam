<?php

use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Kwidoo\Mere\Mere;
use Laravel\Passport\Http\Controllers\AccessTokenController;

Route::post('/signin', RegistrationController::class);
Route::post('/password/reset/', [ResetController::class, 'resetPassword']);
Route::post('/password/change', [ResetController::class, 'changePassword']);
Route::post('/register/{organization:slug?}', RegistrationController::class);
Route::get('translations', TranslationController::class);

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
        // Define specific routes first so they take priority
        Route::get('/auth/user', function (Request $request) {
            return response()->json($request->user());
        });
        Route::get('/auth/organizations', [OrganizationController::class, 'getUserOrganizations']);
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/admin/users/{id}', [UserController::class, 'show']);
        Route::put('/admin/users/{id}', [UserController::class, 'update']);
        Route::post('/admin/users/', [UserController::class, 'store']);
        Route::get('/admin/organizations', [OrganizationController::class, 'index']);
        Route::get('/admin/roles', [RoleController::class, 'index']);

        // Register Mere wildcard routes last
        Mere::registerRoutes();
    }
);
