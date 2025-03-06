<?php

namespace App\Aggregates;

use App\Events\PasswordResetStarted;
use App\Events\PasswordResetVerified;
use Kwidoo\Contacts\Aggregates\ContactAggregateRoot;

class PasswordAggregateRoot extends ContactAggregateRoot
{
    public function startPasswordChange(string $contactUuid, string $verifier): self
    {
        $this->recordThat(new PasswordResetStarted($contactUuid, $verifier));

        return $this;
    }

    public function passwordChanged(string $contactUuid, string $verifier): self
    {
        $this->recordThat(new PasswordResetVerified($contactUuid, $verifier));

        return $this;
    }
}
