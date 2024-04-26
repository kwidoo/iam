<?php

namespace App\Aggregates;

use App\Events\Email\EmailCreated;
use App\Events\Email\EmailRemoved;
use App\Events\Email\PrimaryEmailSet;
use App\Events\Email\PrimaryEmailUnset;
use App\Events\User\UserCreated;
use App\Events\User\UserLoggedIn;
use App\Events\User\UserLoginFailed;
use App\Models\Email;
use App\Models\User;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;
use Illuminate\Support\Str;

class UserAggregate extends AggregateRoot
{
    protected string $uuid;

    /**
     * @param array $data
     *
     * @return self
     */
    public function createUser(array $data): self
    {
        $this->uuid = $data['uuid'];

        $this->recordThat((new UserCreated([
            'uuid' => $data['uuid'],
            'password' => $data['password'],
        ]))->setMetaData(['reference_id' => $data['reference_id']]));

        return $this;
    }

    /**
     * @param array $data
     *
     * @return self
     */
    public function createEmail(array $data): self
    {
        $this->uuid = $data['user_uuid'];
        $emailUuid = Str::uuid();

        $this->recordThat((new EmailCreated([
            'uuid' => $emailUuid->toString(),
            'email' => $data['email'],
            'user_uuid' => $this->uuid,
            'is_primary' => false,
        ]))->setMetaData(['reference_id' => $data['reference_id']]));

        return $this;
    }

    /**
     * @param Email $email
     * @param string $referenceId
     *
     * @return self
     */
    public function unsetPrimaryEmail(Email $email, string $referenceId): self
    {
        $this->recordThat((new PrimaryEmailUnset($email))
            ->setMetaData([
                'reference_id' => $referenceId
            ]));

        return $this;
    }

    public function setPrimaryEmail(Email $email, string $referenceId): self
    {
        $this->recordThat((new PrimaryEmailSet($email))
            ->setMetaData([
                'reference_id' => $referenceId
            ]));

        return $this;
    }

    /**
     * @param Email $email
     * @param string $referenceId
     *
     * @return self
     */
    public function removeEmail(Email $email, string $referenceId): self
    {
        $this->recordThat((new EmailRemoved($email))
            ->setMetaData([
                'reference_id' => $referenceId
            ]));

        return $this;
    }

    /**
     * @param User $user
     * @param array $data
     *
     * @return self
     */
    public function userLoggedIn(User $user, array $data): self
    {
        $this->recordThat((new UserLoggedIn($user, $data))
            ->setMetaData([
                'reference_id' => $data['reference_id']
            ]));

        return $this;
    }

    /**
     * @param User $user
     * @param array $data
     *
     * @return self
     */
    public function userLoginFailed(User $user, array $data): self
    {
        $this->recordThat((new UserLoginFailed($user, $data))
            ->setMetaData([
                'reference_id' => $data['reference_id']
            ]));

        return $this;
    }
}
