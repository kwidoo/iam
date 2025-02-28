<?php

namespace App\Services;

use App\Contracts\Aggregates\UserAggregate;
use App\Contracts\Services\CreateUserService;
use App\Data\Create\EmailData;
use App\Data\Create\OrganizationData;
use App\Data\Create\UserData as CreateUserData;
use App\Data\Update\UserData as UpdateUserData;
use App\Events\Email\EmailCreated;
use App\Events\Organization\OrganizationCreated;
use App\Events\User\AfterUserCreated;
use App\Events\User\UserCreated;
use App\Exceptions\UserCreationException;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateEmailUserService implements CreateUserService
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
            DB::transaction(function () use ($data) {
                $data['email_uuid'] = Str::uuid()->toString();
                $data['email_verified_at'] = null;

                $userAggregate = app()->make(UserAggregate::class)->retrieve($data['user_uuid']);
                $userAggregate
                    ->recordThat(new UserCreated(CreateUserData::from($data)))
                    ->recordThat(new EmailCreated(EmailData::from($data)))
                    ->recordThat(new AfterUserCreated(UpdateUserData::from($data)));


                if (config('iam.with_organization')) {
                    $data['organization_uuid'] = Str::uuid()->toString();
                    $data['organization_name'] = $data['organization_name'] ?? 'default';
                    $userAggregate
                        ->recordThat(new OrganizationCreated(OrganizationData::from($data)));
                }
                $userAggregate->persist();
            });
        } catch (Exception $e) {
            $message = config('app.debug') ? 'User creation failed: ' . $e->getMessage() : 'User creation failed';
            throw new UserCreationException($message, 422, $e);
        }
    }
}
