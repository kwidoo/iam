<?php

namespace App\Http\Controllers;

use App\Contracts\Services\ProfileService;
use App\Contracts\Services\UserService;
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

    public function store(StoreProfileRequest $request)
    {
        $user = $this->userService->create(['password' => bcrypt('password')]);
        $profile = $this->profileService->create([
            ...$request->validated(),
            'user_id' => $user->id
        ]);
        $contactService = $this->factory->make($user);
        $contact = $contactService->create(
            $request->input('method'),
            $request->get('login')
        );

        $organization = Organization::find($request->input('organization_id')) ?? Organization::where('name', 'main')->first();
        if ($organization) {
            $user->organizations()->attach($organization);
            $profile->organizations()->attach($organization);
        }





        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return response()->json(['message' => 'User updated successfully']);
        return $this->userService->update($id, $request->all());
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
