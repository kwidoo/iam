<?php

namespace App\Aggregates\Manage;

use App\Events\User\UserLoggedIn;
use App\Events\User\UserLoginFailed;
use App\Models\User;

trait ManageLogins
{
    /**
     * @param User $user
     * @param array<string,string> $data
     *
     * @return self
     */
    public function userLoggedIn(User $user, array $data): self
    {
        $event = (new UserLoggedIn($user, $data))->setMetaData([
            'reference_id' => $data['reference_id']
        ]);
        event($event);
        $this->recordThat($event);

        return $this;
    }

    /**
     * @param User|null $user
     * @param array<string,string> $data
     *
     * @return self
     */
    public function userLoginFailed(?User $user, array $data): self
    {
        $this->recordThat((new UserLoginFailed($user, $data))
            ->setMetaData([
                'reference_id' => $data['reference_id']
            ]));

        return $this;
    }
}
