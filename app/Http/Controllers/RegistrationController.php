<?php

namespace App\Http\Controllers;

use App\Contracts\Services\RegistrationService;
use App\Data\RegistrationData;
use Illuminate\Http\JsonResponse;

class RegistrationController extends Controller
{
    public function __construct(protected RegistrationService $service) {}

    /**
     * @param RegistrationData $data
     *
     * @return JsonResponse
     */
    public function __invoke(RegistrationData $data): JsonResponse
    {
        $this->service->registerNewUser($data);

        return response()->json(['message' => 'User registered successfully.'], 201);
    }
}
