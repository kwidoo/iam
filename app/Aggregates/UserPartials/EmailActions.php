<?php

namespace App\Aggregates\UserPartials;

use App\Events\Email\AfterEmailCreated;
use App\Events\Email\EmailCreated;
use App\Events\Email\EmailRemoved;
use App\Events\Email\PrimaryEmailSet;
use App\Events\Email\PrimaryEmailUnset;
use App\Models\Email;

trait EmailActions
{
    /**
     * @param array $data
     *
     * @return self
     */
    public function createEmail(array $data): self
    {
        $this->recordThat((new EmailCreated([
            'uuid' => $data['email_uuid'],
            'email' => $data['email'],
            'user_uuid' => $data['user_uuid'],
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
     * @param array $data
     *
     * @return self
     */
    public function updateUserAfterEmailCreated(array $data): self
    {
        $this->recordThat((new AfterEmailCreated($data))
            ->setMetaData([
                'reference_id' => $data['reference_id']
            ]));

        return $this;
    }
}
