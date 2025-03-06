<?php

namespace App\Http\Controllers;

use App\Contracts\Services\PasswordResetService;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class ResetController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string',
        ]);

        $contactModel = config('contacts.model');
        if (!$contactModel) {
            throw new RuntimeException('Unable to determine contact model from configuration.');
        }

        $contact = $contactModel::where('value', ltrim($request->string('username'), '+'))->where('is_primary', 1)->firstOrFail();

        // IMPORTANT!!! Can't reset password for passwordless users
        if (!$contact || !$contact->contactable || $contact->contactable->password === null) {
            throw new RuntimeException('Unable to proceed.', 422);
        }

        $verificationService = app()->make(PasswordResetService::class, ['contact' => $contact]);
        $verificationService->create($contact);

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
        $contact = $contactModel::where('value', ltrim($request->string('username'), '+'))->where('is_primary', 1)->firstOrFail();

        // IMPORTANT!!! Can't reset password for passwordless users
        if (!$contact || !$contact->contactable || $contact->contactable->password === null) {
            throw new RuntimeException('Unable to proceed.', 422);
        }

        $verificationService = app()->make(PasswordResetService::class, ['contact' => $contact]);
        if (!$verificationService->verify($request->input('otp'))) {
            throw new RuntimeException('Invalid OTP.', 422);
        }

        $user = $contact->contactable;
        $user->password = bcrypt($request->input('password'));
        $user->save();

        return response()->json(['message' => 'Password changed successfully.']);
    }
}
