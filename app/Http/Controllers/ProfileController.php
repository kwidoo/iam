<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;
use App\Models\Profile;
use App\Services\CreateProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProfileRequest $request)
    {
        $uuid = Str::uuid()->toString();

        $validated = $request->validated();
        $validated['user_uuid'] = $request->user()->uuid;
        $validated['reference_id'] = $uuid;

        CreateProfileService::createProfile($validated);

        return response()->json([
            'status' => 'ok',
            'reference' => $uuid
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Profile $profile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Profile $profile)
    {
        //
    }
}
