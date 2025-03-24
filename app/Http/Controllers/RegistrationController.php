<?php

namespace App\Http\Controllers;

use App\Contracts\Services\RegistrationService;
use App\Exceptions\UserCreationException;
use App\Http\Requests\RegisterRequest;
use App\Models\Organization;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Kwidoo\Contacts\Contracts\Contact;

class RegistrationController extends Controller
{
    public function __construct(protected RegistrationService $service) {}

    public function __invoke(RegisterRequest $request, ?Organization $organization = null): JsonResponse
    {
        DB::beginTransaction();
        try {
            $method = $request->input('method');

            if (!method_exists($this, 'registerFor' . ucfirst($method)) || !config('iam.allow_' . $method)) {
                throw new UserCreationException('Invalid registration method.');
            }
            $contact = $this->{'registerFor' . ucfirst($method)}($request, $organization);
            DB::commit();

            event('core.user.registered', [$contact]);

            return response()->json([
                'message' => 'User registered successfully.'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Registration failed.'], 500);
        }
    }

    /**
     * @param RegisterRequest $request
     *
     * @return \Kwidoo\Contacts\Models\Contact
     */
    protected function registerForEmail(RegisterRequest $request, ?Organization $organization): Contact
    {
        $password = $request->input('otp') ? null : Hash::make($request->input('password'));
        $user = $this->service->create([
            'password' => $password,
            'type'  => 'email',
            'value' => $request->input('email'),
            'organization' => $organization,
        ]);

        return $user->contacts()->first();
    }

    /**
     * @param RegisterRequest $request
     *
     * @return \Kwidoo\Contacts\Models\Contact
     */
    protected function registerForPhone(RegisterRequest $request, ?Organization $organization): Contact
    {
        $user = $this->service->registerNewUser([
            'password' => Hash::make($request->input('password')),
            'type'  => 'phone',
            'value' => ltrim($request->input('phone'), '+'),
            'organization' => $organization,
        ]);

        return $user->contacts()->first();
    }
}
