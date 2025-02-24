<?php

namespace App\Http\Controllers;

use App\Contracts\Models\UserWriteModel;
use App\Contracts\Services\CreateUserService;
use App\Contracts\Services\TwilioService;
use App\Http\Controllers\Traits\PhoneChallenge;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\Middleware;
use Exception;

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
        protected CreateUserService $createUserService,
        protected TwilioService $twilioService
    ) {}

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

        if ($validated['type'] === 'phone') {
            return $this->makePhoneChallenge($validated);
        }

        if (!config('iam.use_password', true)) {
            // send link
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
    public function update(UpdateUserRequest $request, UserWriteModel $user): void
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
    public function destroy(UserWriteModel $user): void
    {
        info('User destroy called', ['user' => $user->uuid]);
        throw new Exception('Not implemented');
    }
}
