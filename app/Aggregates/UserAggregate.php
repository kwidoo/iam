<?php

namespace App\Aggregates;

use App\Aggregates\UserPartials\EmailActions;
use App\Aggregates\UserPartials\LoginActions;
use App\Aggregates\UserPartials\UserActions;
use App\Contracts\CreateEmail;
use App\Contracts\UserAggregate as ContractsUserAggregate;
use App\Events\Organization\OrganizationCreated;
use App\Events\Profile\ProfileCreated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class UserAggregate extends AggregateRoot implements ContractsUserAggregate, CreateEmail
{
    use EmailActions;
    use LoginActions;
    use UserActions;

    /**
     * @param array $data
     *
     * @return self
     */
    public function createOrganization(array $data): self
    {
        $this->recordThat((new OrganizationCreated([
            'uuid' => $data['organization_uuid'],
            'name' => $data['organization_name'],
            'user_uuid' => $data['user_uuid'],
            'data' => [
                'organization_name' => $data['organization_name'],
                'organization_uuid' => $data['organization_uuid'],
                'user_uuid' => $data['user_uuid'],
            ],
        ]))->setMetaData([
            'reference_id' => $data['reference_id']
        ]));

        return $this;
    }

    /**
     * @param string $uuid
     *
     * @return self
     */
    public function createProfile(array $data): self
    {
        $this->recordThat((new ProfileCreated([
            'uuid' => $data['profile_uuid'],
            'name' => $data['profile_name'],
            'user_uuid' => $data['user_uuid'],
            'organization_uuid' => $data['organization_uuid'],
            'data' => [
                'profile_name' => $data['profile_name'],
                'profile_uuid' => $data['profile_uuid'],
                'user_uuid' => $data['user_uuid'],
                'organization_uuid' => $data['organization_uuid'],
            ],
        ]))->setMetaData([
            'reference_id' => $data['reference_id']
        ]));

        return $this;
    }
}
