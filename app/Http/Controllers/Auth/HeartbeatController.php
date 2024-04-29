<?php

namespace App\Http\Controllers\Autn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HeartbeatController extends Controller
{
    public function __invoke(Request $request)
    {
        return response()->json(['status' => 'ok', 'user_uuid' => $request->user()->uuid]);
    }
}
