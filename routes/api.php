<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PermissionCheckController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\Auth\IntrospectionController;
use App\Http\Controllers\Auth\UserInfoController;
use App\Http\Middleware\ClientCredentialsMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Kwidoo\Mere\Mere;
use Laravel\Passport\Http\Controllers\AccessTokenController;

Route::post('/signin', RegistrationController::class);
Route::post('/password/reset/', [ResetController::class, 'resetPassword']);
Route::post('/password/change', [ResetController::class, 'changePassword']);
Route::post('/register/{organization:slug?}', RegistrationController::class);
Route::get('translations', TranslationController::class);

// Public key endpoint for token verification by other services
Route::get('/auth/public-key', [AuthController::class, 'publicKey']);
Route::post('/auth/introspect', [AuthController::class, 'introspect']);

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

        // Add token revocation endpoint
        Route::post('/revoke', [
            'uses' => \App\Http\Controllers\Auth\RevocationController::class . '@revoke',
            'as' => 'token.revoke',
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

        // Admin User Management routes
        Route::get('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'index']);
        Route::get('/admin/users/{id}', [UserController::class, 'show']);
        Route::put('/admin/users/{id}', [UserController::class, 'update']);
        Route::post('/admin/users/', [UserController::class, 'store']);
        Route::delete('/admin/users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy']);
        Route::get('/admin/roles', [RoleController::class, 'index']);
        Route::post('/admin/users/{id}/roles', [\App\Http\Controllers\Admin\UserController::class, 'assignRoles']);
        Route::delete('/admin/users/{id}/roles', [\App\Http\Controllers\Admin\UserController::class, 'removeRoles']);

        // Admin OAuth Client Management routes
        Route::get('/admin/clients', [\App\Http\Controllers\Admin\ClientController::class, 'index']);
        Route::post('/admin/clients', [\App\Http\Controllers\Admin\ClientController::class, 'store']);
        Route::get('/admin/clients/{id}', [\App\Http\Controllers\Admin\ClientController::class, 'show']);
        Route::put('/admin/clients/{id}', [\App\Http\Controllers\Admin\ClientController::class, 'update']);
        Route::delete('/admin/clients/{id}', [\App\Http\Controllers\Admin\ClientController::class, 'destroy']);
        Route::post('/admin/clients/{id}/rotate-secret', [\App\Http\Controllers\Admin\ClientController::class, 'rotateSecret']);

        Route::get('/admin/organizations', [OrganizationController::class, 'index']);

        // Permission check API endpoints
        Route::post('/auth/check-permission', [PermissionCheckController::class, 'checkOrgPermission']);
        Route::post('/auth/check-permissions', [PermissionCheckController::class, 'checkMultipleOrgPermissions']);
        Route::get('/auth/organizations/{organization}/permissions', [PermissionCheckController::class, 'getUserOrgPermissions']);

        // User permission management routes
        Route::get('/admin/users/{user}/permissions', [UserPermissionController::class, 'getUserPermissions']);
        Route::get('/admin/users/{user}/organizations/{organization}/permissions', [UserPermissionController::class, 'getOrganizationPermissions'])
            ->middleware('org.permission:permission.view');
        Route::post('/admin/users/{user}/permissions', [UserPermissionController::class, 'assignPermissions']);
        Route::delete('/admin/users/{user}/permissions', [UserPermissionController::class, 'revokePermissions']);

        // Organization management routes with organization-aware permission middleware
        Route::get('/admin/organizations/{organization}/users', [OrganizationController::class, 'getUsers'])
            ->middleware('org.permission:organization.view');
        Route::post('/admin/organizations/{organization}/users', [OrganizationController::class, 'addUser'])
            ->middleware('org.permission:organization.edit');
        Route::delete('/admin/organizations/{organization}/users/{user}', [OrganizationController::class, 'removeUser'])
            ->middleware('org.permission:organization.edit');
        Route::put('/admin/organizations/{organization}/users/{user}/role', [OrganizationController::class, 'updateUserRole'])
            ->middleware('org.permission:organization.edit');
        Route::get('/admin/organizations/{organization}/roles', [OrganizationController::class, 'getRoles'])
            ->middleware('org.permission:role.view');
        Route::get('/admin/organizations/{organization}/permissions', [OrganizationController::class, 'getPermissions'])
            ->middleware('org.permission:permission.view');
        Route::post('/admin/organizations/{organization}/generate-permissions', [PermissionController::class, 'generateForOrganization'])
            ->middleware('org.permission:permission.create');

        // Roles and Permissions routes
        Route::get('/admin/roles', [RoleController::class, 'index']);
        Route::post('/admin/roles', [RoleController::class, 'store']);
        Route::get('/admin/roles/{role}', [RoleController::class, 'show']);
        Route::put('/admin/roles/{role}', [RoleController::class, 'update']);
        Route::delete('/admin/roles/{role}', [RoleController::class, 'destroy']);
        Route::get('/admin/roles/{role}/permissions', [RoleController::class, 'getPermissions']);
        Route::post('/admin/roles/{role}/permissions', [RoleController::class, 'assignPermissions']);
        Route::delete('/admin/roles/{role}/permissions/{permission}', [RoleController::class, 'removePermission']);

        // Permissions routes
        Route::get('/admin/permissions', [PermissionController::class, 'index']);
        Route::post('/admin/permissions', [PermissionController::class, 'store']);
        Route::get('/admin/permissions/{permission}', [PermissionController::class, 'show']);
        Route::put('/admin/permissions/{permission}', [PermissionController::class, 'update']);
        Route::delete('/admin/permissions/{permission}', [PermissionController::class, 'destroy']);
        Route::get('/admin/permissions/{permission}/roles', [PermissionController::class, 'getRoles']);

        // Register Mere wildcard routes last
        Mere::registerRoutes();
    }
);

/*
|--------------------------------------------------------------------------
| OIDC & OAuth2 Endpoints
|--------------------------------------------------------------------------
|
| Routes for OpenID Connect and OAuth 2.0 endpoints.
|
*/

// UserInfo endpoint (OIDC compliant)
Route::middleware(['auth:api', 'throttle:60,1'])
    ->get('/userinfo', [UserInfoController::class, 'me'])
    ->name('auth.userinfo');

// Alternative endpoint name for UserInfo (often used in OIDC implementations)
Route::middleware(['auth:api', 'throttle:60,1'])
    ->get('/me', [UserInfoController::class, 'me'])
    ->name('auth.me');

// Token Introspection endpoint (RFC 7662)
Route::middleware([ClientCredentialsMiddleware::class, 'throttle:120,1'])
    ->post('/introspect', [IntrospectionController::class, 'introspect'])
    ->name('auth.introspect');
