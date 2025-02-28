<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMicroServiceRequest;
use App\Services\CreateMicroService;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function store(StoreMicroServiceRequest $request, CreateMicroService $createMicroService)
    {
        /** @var \Laravel\Passport\Guards\TokenGuard $auth */
        $auth = auth('api');
        /** @var \Laravel\Passport\Client $client */
        $client = $auth->client();

        if (!$client) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }
        $referenceId = Str::uuid()->toString();

        $validated = $request->validated();
        $validated['service_uuid'] = $referenceId;
        $validated['client_id'] = $client->id;
        $validated['reference_id'] = Str::uuid()->toString();

        $createMicroService->create($validated);

        return response()->json([
            'status' => 'ok',
            'reference' => $referenceId
        ]);
    }
}
