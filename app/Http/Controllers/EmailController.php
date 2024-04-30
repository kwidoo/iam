<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmailRequest;
use App\Models\Email;
use App\Contracts\AddEmailService;
use App\Contracts\RemoveEmailService;
use App\Contracts\SendEmailVerificationService;
use App\Contracts\VerifyEmailService;
use App\Http\Requests\SendEmailVerificationRequest;
use App\Services\SetPrimaryEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class EmailController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmailRequest $request, AddEmailService $addEmailService): JsonResponse
    {
        $referenceId = Str::uuid()->toString();

        $addEmailService(
            $request->user(),
            $request->input('email'),
            $referenceId
        );

        return response()->json([
            'status' => 'ok',
            'reference' => $referenceId
        ]);
    }

    /**
     * @param Request $request
     * @param VerifyEmailService $verifyEmailService
     *
     * @return [type]
     */
    public function confirm(Request $request, VerifyEmailService $verifyEmailService): JsonResponse
    {
        $referenceId = Str::uuid()->toString();

        $email = Email::findOrFail($request->query('id'));

        $verifyEmailService($email);

        return response()->json(['status' => 'ok', 'reference' => $referenceId]);
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(SendEmailVerificationRequest $request, SendEmailVerificationService $emailVerification): JsonResponse
    {
        $referenceId = Str::uuid()->toString();

        $email = Email::where('email', $request->input('email'))->first();
        if ($email->email_verified_at === null) {
            $emailVerification($email);
        }

        return response()->json(['status' => 'ok', 'reference' => $referenceId]);
    }

    public function setPrimary(Email $email, SetPrimaryEmailService $primaryEmailService): JsonResponse
    {
        $uuid = Str::uuid()->toString();
        if (!$email->is_primary) {
            $primaryEmailService($email, $uuid);
        }

        return response()->json([
            'status' => 'ok',
            'reference' => $uuid
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Email $email, RemoveEmailService $removeEmailService): JsonResponse
    {
        $referenceId = Str::uuid()->toString();
        $removeEmailService($email, $referenceId);

        return response()->json([
            'status' => 'ok',
            'reference' => $referenceId
        ]);
    }
}
