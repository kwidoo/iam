<?php

use App\Http\Controllers\Auth\AuthorizationController;
use Illuminate\Support\Facades\Route;

// OAuth 2.0 Authorization Code Grant Routes
Route::middleware(['web', 'auth'])->prefix('oauth')->group(function () {
    // Use our custom authorization controller
    Route::get('/authorize', [
        'uses' => AuthorizationController::class . '@authorize',
        'as' => 'passport.authorizations.authorize',
        'middleware' => ['oauth2.validate-redirect'],
    ]);

    Route::post('/authorize', [
        'uses' => AuthorizationController::class . '@approveAuthorization',
        'as' => 'passport.authorizations.approve',
        'middleware' => ['oauth2.verify-state'],
    ]);

    Route::delete('/authorize', [
        'uses' => AuthorizationController::class . '@denyAuthorization',
        'as' => 'passport.authorizations.deny',
    ]);
});

// Default route to serve the frontend
Route::get('/{any}', function () {
    return view('index'); // Serves index.blade.php
})->where('any', '^(?!api|oauth).*$'); // Excludes 'api' and 'oauth' routes
