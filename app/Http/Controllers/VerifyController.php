<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kwidoo\Contacts\Contracts\Contact; //<-- should be Contract
use Kwidoo\Contacts\Contracts\VerificationService;

class VerifyController extends Controller
{
    public function send(Contact $contact)
    {
        if ($contact->isVerified()) {
            return response()->json([
                'message' => 'Contact already verified.'
            ], 400);
        }
        $verificationService = app()->make(VerificationService::class, ['contact' => $contact]);
        $verificationService->create($contact);

        return response()->json([
            'message' => "Verification link sent to your {$contact->type}."
        ], 201);
    }

    public function verify(Request $request, Contact $contact)
    {
        $verificationService = app()->make(VerificationService::class, ['contact' => $contact]);
        $verified = $verificationService->verify($request->input('otp'));

        if ($verified) {
            return response()->json([
                'message' => 'Verification successful.'
            ]);
        }

        return response()->json([
            'message' => 'Verification failed.'
        ], 400);
    }
}
