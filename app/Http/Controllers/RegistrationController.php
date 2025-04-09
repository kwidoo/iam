<?php

namespace App\Http\Controllers;

use App\Contracts\Services\RegistrationService;
use App\Data\RegistrationData;
use Illuminate\Http\JsonResponse;

/**
 * Controller responsible for handling user registration requests.
 * Provides endpoints for user registration with different flows and methods.
 *
 * @category App\Http\Controllers
 * @package  App\Http\Controllers
 * @author   John Doe <john.doe@example.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/your-repo
 */
class RegistrationController extends Controller
{
    /**
     * Initialize the registration controller with required service.
     *
     * @param RegistrationService $service Registration service instance
     */
    public function __construct(protected RegistrationService $service)
    {
    }

    /**
     * Handle the registration request.
     * Processes the registration data and returns appropriate response.
     *
     * @param RegistrationData $data Registration data from request
     *
     * @return JsonResponse
     */
    public function __invoke(RegistrationData $data): JsonResponse
    {
        $this->service->registerNewUser($data);

        return response()->json(['message' => 'User registered successfully.'], 201);
    }
}
