<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Jobs\CreateUserJob;
use App\Models\User;
use App\Services\CreateUserService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', except: ['store']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function heartBeat()
    {
        return response()->json('', 200);
    }

    public function getUser(Request $request)
    {
        return new UserResource($request->user()->load('roles.permissions'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated['user_uuid'] = Str::uuid()->toString();
        $validated['password'] = bcrypt($validated['password']);
        $validated['reference_id'] = Str::uuid()->toString();

        CreateUserService::createUser($validated);
        return ['status' => 'ok', 'reference_id' => $validated['reference_id']];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
