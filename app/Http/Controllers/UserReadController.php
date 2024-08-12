<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserReadController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(
            (new UserProfile)
                ->find(
                    $request->user()?->uuid
                )
        );
    }
}
