<?php

namespace App\Aggregates\Manage;

use App\Data\Create\UserData;
use App\Events\User\AfterUserCreated;
use App\Events\User\UserCreated;

trait ManageUsers
{
    /**
     * @param array $data
     *
     * @return self
     */
    public function createUser(UserData $userData): self
    {
        $this->recordThat(
            (new UserCreated($userData))
                ->setMetaData(['reference_id' => $userData->referenceId])
        );

        return $this;
    }


    /**
     * @param array $data
     *
     * @return self
     */
    public function updateUserAfterCreated(array $data): self
    {
        $this->recordThat((new AfterUserCreated($data))->setMetaData(['reference_id' => $data['reference_id']]));

        return $this;
    }
}
