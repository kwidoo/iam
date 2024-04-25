<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmEmailRequest;
use App\Http\Requests\SetPrimaryEmailRequest;
use App\Http\Requests\StoreEmailRequest;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class EmailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $emails = $request->user()->emails;

        return response()->json($emails);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmailRequest $request)
    {
        $data = [
            'email' => $request->input('email'),
            'user_uuid' => $request->user()->uuid,
        ];

        $email = Email::createEmail($data);

        return response()->json([
            'message' => 'Email created',
            'email' => $email->email,
            'user_uuid' => $email->user_uuid
        ]);
    }

    public function confirm(Request $request)
    {
        return response()->json(['message' => 'Email confirmed']);
    }

    public function setPrimary(Email $email)
    {
        $email->unsetPrimaryEmail();
        return response()->json(['message' => 'Email set as primary']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Email $email)
    {
        return response()->json([
            'message' => 'Email retrieved',
            'email' => $email->email,
            'user_uuid' => $email->user_uuid
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Email $email)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Email $email)
    {
        $email->removeEmail();

        return response()->json(['message' => 'Email removed']);
    }
}
