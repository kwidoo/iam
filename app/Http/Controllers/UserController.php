<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CreateUserService;
use App\Http\Controllers\Traits\PhoneChallenge;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    use PhoneChallenge;

    /**
     * UserController constructor.
     */
    public function __construct(
        protected CreateUserService $createUserService
    ) {
    }

    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int,Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', except: ['store']),
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['user_uuid'] = Str::uuid()->toString();
        $validated['reference_id'] = Str::uuid()->toString();

        if (config('iam.user_field') === 'phone') {
            $this->makePhoneChallenge($validated);

            return response()->json([
                'status' => 'ok',
                'reference' => $validated['reference_id'],
            ]);
        }

        ($this->createUserService)($validated);
        return response()->json([
            'status' => 'ok',
            'reference' => $validated['reference_id'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     *
     * @return void
     */
    public function update(UpdateUserRequest $request, User $user): void
    {
        info('User update called', ['user' => $user->uuid, ...$request->validated()]);
        throw new Exception('Not implemented');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     *
     * @return void
     */
    public function destroy(User $user): void
    {
        info('User destroy called', ['user' => $user->uuid]);
        throw new Exception('Not implemented');
    }
}
