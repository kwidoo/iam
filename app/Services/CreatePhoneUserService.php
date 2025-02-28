<?php

namespace App\Services;

use App\Contracts\Aggregates\UserAggregate;
use App\Contracts\Services\CreateUserService;
use App\Data\Create\OrganizationData;
use App\Data\Create\PhoneData;
use App\Data\Create\UserData as CreateUserData;
use App\Data\Update\UserData as UpdateUserData;
use App\Events\Organization\OrganizationCreated;
use App\Events\Phone\PhoneCreated;
use App\Events\User\AfterUserCreated;
use App\Events\User\UserCreated;
use App\Exceptions\UserCreationException;
use App\Exceptions\UserLoginException;
use App\Models\Phone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class CreatePhoneUserService implements CreateUserService
{
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
            $phone = Phone::findByFullPhone($data['full_phone']);
            if ($phone && !$phone->is_primary) {
                throw new UserLoginException('Phone number is not primary', 422);
            }

            DB::transaction(function () use ($data, $phone) {
                if (!$phone) {
                    $data['user_uuid'] = Str::uuid()->toString();


                    $userAggregate = app()->make(UserAggregate::class)->retrieve($data['user_uuid']);
                    $userAggregate
                        ->recordThat(new UserCreated(CreateUserData::from($data)))
                        ->recordThat(new PhoneCreated(PhoneData::from($data)))
                        ->recordThat(new AfterUserCreated(UpdateUserData::from($data)));


                    if (config('iam.with_organization')) {
                        if (!isset($data['organization_uuid'])) {
                            $data['organization_uuid'] = Str::uuid()->toString();
                        }
                        $data['organization_name'] = $data['organization_name'] ?? 'default';
                        $userAggregate
                            ->recordThat(new OrganizationCreated(OrganizationData::from($data)));
                    }
                    $userAggregate->persist();
                }
            });
        } catch (UserLoginException $e) {
            throw $e;
        } catch (Exception $e) {
            $message = config('app.debug') ? 'User creation failed: ' . $e->getMessage() : 'User creation failed';
            throw new UserCreationException($message, 422, $e);
        }
    }

    // protected function createUser()
}
