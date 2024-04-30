<?php

namespace App\Services;

use App\Contracts\CreateUserService;
use App\Contracts\UserAggregate;
use App\Exceptions\UserCreationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateRootUserService implements CreateUserService
{
    public function __construct(protected UserAggregate $userAggregate)
    {
        //
    }

    /**
     * @param array $data
     *
     * @return void
     *
     * @throws UserCreationException
     */
    public function __invoke(array $data): void
    {
        try {
            DB::transaction(function () use ($data) {

                $data['email_uuid'] = Str::uuid()->toString();
                $data['organization_uuid'] = Str::uuid()->toString();
                $data['organization_name'] = $data['organization_name'] ?? 'default';
                $data['profile_uuid'] = Str::uuid()->toString();
                $data['profile_name'] = $data['profile_name'] ?? 'default';

                $this->userAggregate->retrieve($data['user_uuid'])
                    ->createUser($data)
                    ->createEmail($data)
                    ->createOrganization($data)
                    ->createProfile($data)
                    ->updateUserAfterCreated($data)
                    //
                    ->persist($data['reference_id']);
            });
        } catch (\Exception $e) {
            $message = config('app.debug') ? 'User creation failed: ' . $e->getMessage() : 'User creation failed';
            throw new UserCreationException($message, 422, $e);
        }
    }
}
