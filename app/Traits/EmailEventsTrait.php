<?php

namespace App\Traits;

use App\Events\Email\EmailConfirmed;
use App\Events\Email\EmailCreated;
use App\Events\Email\EmailRemoved;
use App\Events\Email\PrimaryEmailSet;
use App\Events\Email\PrimaryEmailUnset;
use App\Models\Email;

trait EmailEventsTrait
{
    /**
     * @param string $uuid
     * @param string $email
     *
     * @return Email
     */
    public static function createEmail(array $data): Email
    {
        $data['uuid'] = (new self)->newUniqueId();

        event(new EmailCreated($data));

        return static::find($data['uuid']);
    }

    /**
     * @param string $uuid
     * @param string $emailUuid
     *
     * @return void
     */
    public function confirmEmail(): void
    {
        event(new EmailConfirmed($this));
    }

    /**
     * @param string $referenceId
     *
     * @return void
     */
    public function removeEmail(string $referenceId): void
    {
        event((new EmailRemoved($this))
            ->setMetaData([
                'reference_id' => $referenceId
            ]));
    }

    /**
     * @param string $uuid
     * @param string $emailUuid
     *
     * @return void
     */
    public function setPrimaryEmail(): void
    {
        event(new PrimaryEmailSet($this));
    }

    /**
     * @param string $uuid
     * @param string $emailUuid
     *
     * @return void
     */
    public function unsetPrimaryEmail(): void
    {
        event(new PrimaryEmailUnset($this));
    }
}
