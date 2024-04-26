<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmailRequest;
use App\Models\Email;
use App\Services\AddEmailService;
use App\Services\RemoveEmailService;
use App\Services\SetPrimaryEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;


class EmailController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmailRequest $request)
    {
        $uuid = Str::uuid()->toString();

        AddEmailService::addEmail($request->user(), $request->input('email'), $uuid);

        return response()->json([
            'status' => 'ok',
            'reference' => $uuid
        ]);
    }

    public function confirm(Request $request)
    {
        return response()->json(['message' => 'Email confirmed']);
    }

    public function setPrimary(Email $email)
    {
        $uuid = Str::uuid()->toString();
        if (!$email->is_primary) {
            SetPrimaryEmailService::setPrimaryEmail($email, $uuid);
        }

        return response()->json([
            'status' => 'ok',
            'reference' => $uuid
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Email $email)
    {
        throw new Exception('Not implemented');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Email $email)
    {
        $uuid = Str::uuid()->toString();
        RemoveEmailService::removeEmail($email, $uuid);

        return response()->json([
            'status' => 'ok',
            'reference' => $uuid
        ]);
    }
}
