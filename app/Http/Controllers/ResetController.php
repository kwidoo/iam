<?php

namespace App\Http\Controllers;

use App\Factories\PasswordResetContext;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kwidoo\Contacts\Contracts\ContactRepository;
use Kwidoo\Contacts\Contracts\VerificationServiceFactory;
use RuntimeException;

class ResetController extends Controller
{
    public function __construct(
        protected ContactRepository $repository,
        protected VerificationServiceFactory $factory,
        protected PasswordResetContext $context
    ) {}
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
        ]);

        $contact = $this->repository
            ->where('value', ltrim($request->string('username'), '+'))
            ->where('is_primary', 1)
            ->firstOrFail();

        // IMPORTANT!!! Can't reset password for passwordless users
        if (!$contact || !$contact->contactable || $contact->contactable->password === null) {
            throw new RuntimeException('Unable to proceed.', 422);
        }

        $verificationService = $this->factory->make($contact, $this->context);
        $verificationService->create();

        return response()->json(['message' => 'Password reset link sent to your email.']);
    }

    /**
     * @param ChangePasswordRequest $request
     *
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $contactModel = config('contacts.model');
        if (!$contactModel) {
            throw new RuntimeException('Unable to determine contact model from configuration.');
        }
        $contact = $this->repository
            ->where('value', ltrim($request->string('username'), '+'))
            ->where('is_primary', 1)
            ->firstOrFail();

        // IMPORTANT!!! Can't reset password for passwordless users
        if (!$contact || !$contact->contactable || $contact->contactable->password === null) {
            throw new RuntimeException('Unable to proceed.', 422);
        }

        $verificationService = $this->factory->make($contact, $this->context);
        if (!$verificationService->verify($request->input('otp'))) {
            throw new RuntimeException('Invalid OTP.', 422);
        }

        $user = $contact->contactable;
        $user->password = bcrypt($request->input('password'));
        $user->save();

        return response()->json(['message' => 'Password changed successfully.']);
    }
}
