<?php

namespace App\Http\Controllers;

use App\Data\RegistrationData;

use App\Factories\RegisterStrategyFactory;
use Illuminate\Http\JsonResponse;

class RegistrationController extends Controller
{
    public function __construct(protected RegisterStrategyFactory $resolver) {}

    /**
     * @param RegistrationData $data
     *
     * @return JsonResponse
     */
    public function __invoke(RegistrationData $data): JsonResponse
    {
        $strategy = $this->resolver->resolve($data->method);
        $strategy->register($data);

        return response()->json(['message' => 'User registered successfully.'], 201);
    }
}
