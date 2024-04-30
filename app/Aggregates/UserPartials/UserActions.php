<?php

namespace App\Aggregates\UserPartials;

use App\Events\AfterUserCreated;
use App\Events\User\UserCreated;

trait UserActions
{
    /**
     * @param array $data
     *
     * @return self
     */
    public function createUser(array $data): self
    {
        $this->recordThat((new UserCreated([
            'uuid' => $data['user_uuid'],
            'password' => $data['password'],
        ]))->setMetaData(['reference_id' => $data['reference_id']]));

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
