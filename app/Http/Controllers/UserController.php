<?php

namespace App\Http\Controllers;

use App\Contracts\Services\ProfileService;
use App\Contracts\Services\RegistrationService;
use App\Contracts\Services\UserService;
use App\Data\RegistrationData;
use App\Http\Requests\StoreProfileRequest;
use App\Models\Organization;
use Illuminate\Http\Request;
use Kwidoo\Contacts\Contracts\ContactServiceFactory;
use Kwidoo\Mere\Data\ListQueryData;
use Kwidoo\Mere\Data\ShowQueryData;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected ProfileService $profileService,
        protected ContactServiceFactory $factory,
        protected RegistrationService $registrationService
    ) {}
    /**
     * Handle the incoming request.
     */
    public function index(ListQueryData $query)
    {
        return $this->userService->list($query);
    }

    public function show(ShowQueryData $query)
    {
        return $this->userService->getById($query);
    }

    public function store(RegistrationData $data)
    {
        $this->registrationService->registerNewUser($data);

        return response()->json(['message' => 'User updated successfully']);
    }

    public function update(Request $request, $id)
    {
        $profile = $this->profileService->findByUserId($id);
        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }
        $profile->fill($request->only('fname', 'lname', 'dob', 'gender'));
        $profile->save();
        return response()->json(['message' => 'User updated successfully']);
        return $this->userService->update($id, $request->all());
    }
}
