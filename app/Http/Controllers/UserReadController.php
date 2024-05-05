<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;

class UserReadController extends Controller
{
    public function __invoke(Request $request)
    {
        info($request->user()?->token()?->client?->id);
        return UserProfile::find($request->user()->uuid);
    }
}
