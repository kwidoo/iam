<?php

namespace App\Aggregates\Manage;

use App\Data\Create\UserData;
use App\Data\Update\UserData as UpdateUserData;
use App\Events\User\AfterUserCreated;
use App\Events\User\UserCreated;

trait ManageUsers
{
    /**
     * @param UserData $userData
     *
     * @return self
     */
    public function createUser(UserData $userData): self
    {
        $this->recordThat(
            (new UserCreated($userData))
                ->setMetaData([
                    'reference_id' => $userData->referenceId
                ])
        );

        return $this;
    }


    /**
     * @param UpdateUserData $userData
     *
     * @return self
     */
    public function updateUserAfterCreated(UpdateUserData $userData): self
    {
        $this->recordThat(
            (new AfterUserCreated($userData))
                ->setMetaData([
                    'reference_id' => $userData->referenceId
                ])
        );

        return $this;
    }
}
