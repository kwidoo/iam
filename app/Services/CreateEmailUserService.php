<?php

namespace App\Services;

use App\Contracts\Aggregates\UserAggregate;
use App\Contracts\Services\CreateUserService;
use App\Data\Create\EmailData;
use App\Data\Create\OrganizationData;
use App\Data\Create\ProfileData;
use App\Data\Create\UserData;
use App\Data\Update\UserData as UpdateUserData;
use App\Exceptions\UserCreationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateEmailUserService implements CreateUserService
{
    public function __construct(protected UserAggregate $userAggregate)
    {
        //
    }

    /**
     * @param array<string,string> $data
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
                $data['email_verified_at'] = null;
                $data['organization_uuid'] = Str::uuid()->toString();
                $data['organization_name'] = $data['organization_name'] ?? 'default';
                $data['profile_uuid'] = Str::uuid()->toString();
                $data['profile_name'] = $data['profile_name'] ?? 'default';
                $data['type'] = $data['type'] ?? 'default';

                $this->userAggregate->retrieve((string) $data['user_uuid'])
                    ->createUser(UserData::from($data))
                    ->createEmail(EmailData::from($data))
                    ->createOrganization(OrganizationData::from($data))
                    ->createProfile(ProfileData::from($data))
                    ->updateUserAfterCreated(UpdateUserData::from($data))
                    //
                    ->persist();
            });
        } catch (\Exception $e) {
            $message = config('app.debug') ? 'User creation failed: ' . $e->getMessage() : 'User creation failed';
            throw new UserCreationException($message, 422, $e);
        }
    }
}
