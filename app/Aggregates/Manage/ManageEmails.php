<?php

namespace App\Aggregates\Manage;

use App\Data\Create\EmailData;
use App\Data\Update\EmailData as UpdateEmailData;
use App\Events\Email\AfterEmailCreated;
use App\Events\Email\EmailCreated;
use App\Events\Email\EmailRemoved;
use App\Events\Email\EmailVerified;
use App\Events\Email\PrimaryEmailSet;
use App\Events\Email\PrimaryEmailUnset;
use App\Events\Email\VerifyEmail;
use App\Models\Email;

trait ManageEmails
{
    /**
     * @param array $data
     *
     * @return self
     */
    public function createEmail(EmailData $emailData): self
    {
        $this->recordThat(
            (new EmailCreated($emailData))
                ->setMetaData([
                    'reference_id' => $emailData->referenceId
                ])
        );

        return $this;
    }

    /**
     * @param Email $email
     * @param string $referenceId
     *
     * @return self
     */
    public function unsetPrimaryEmail(UpdateEmailData $emailData): self
    {
        $this->recordThat((new PrimaryEmailUnset($emailData))
            ->setMetaData([
                'reference_id' => $emailData->referenceId
            ]));

        return $this;
    }

    public function setPrimaryEmail(UpdateEmailData $emailData): self
    {
        $this->recordThat((new PrimaryEmailSet($emailData))
            ->setMetaData([
                'reference_id' => $emailData->referenceId
            ]));

        return $this;
    }

    /**
     * @param Email $email
     * @param string $referenceId
     *
     * @return self
     */
    public function removeEmail(UpdateEmailData $emailData): self
    {
        $this->recordThat((new EmailRemoved($emailData))
            ->setMetaData([
                'reference_id' => $emailData->referenceId
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

    /**
     * @param Email $email
     * @param string $referenceId
     *
     * @return self
     */
    public function sendEmailVerification(Email $email, string $referenceId): self
    {
        $this->recordThat((new VerifyEmail($email))
            ->setMetaData([
                'reference_id' => $referenceId
            ]));

        return $this;
    }

    /**
     * @param Email $email
     *
     * @return self
     */

    public function verifyEmail(Email $email, string $referenceId): self
    {
        $this->recordThat((new EmailVerified($email))
            ->setMetaData([
                'reference_id' => $referenceId
            ]));

        return $this;
    }
}
