<?php

namespace App\Projectors;

use App\Events\Email\EmailConfirmed;
use App\Events\Email\EmailCreated;
use App\Events\Email\EmailRemoved;
use App\Events\Email\PrimaryEmailSet;
use App\Events\Email\PrimaryEmailUnset;
use App\Events\Email\VerifyEmail;
use App\Models\Email;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class EmailProjector extends Projector
{
    /**
     * @param EmailCreated $event
     *
     * @return void
     */
    public function onEmailCreated(EmailCreated $event): void
    {
        $email = (new Email($event->data));
        $email->writeable()->save();
    }

    /**
     * @param VerifyEmail $event
     *
     * @return void
     */
    public function onVerifyEmailSend(VerifyEmail $event): void
    {
        $email = $event->email;
        $email->notify(new VerifyEmailNotification);
    }

    /**
     * @param EmailConfirmed $event
     *
     * @return void
     */
    public function onEmailConfirmed(EmailConfirmed $event): void
    {
        $email = Email::find($event->emailUuid);
        $email->email_verified_at = now();
        if ($email->user?->has_primary_email !== true) {
            $email->is_primary = true;
        }
        $email->writeable()->save();
    }

    /**
     * @param EmailRemoved $event
     *
     * @return void
     */
    public function onEmailRemoved(EmailRemoved $event): void
    {
        $email = $event->email;
        $email->writeable()->delete();
    }

    /**
     * @param PrimaryEmailSet $event
     *
     * @return void
     */
    public function onEmailPrimarySet(PrimaryEmailSet $event): void
    {
        $email = $event->email;
        $email->is_primary = true;
        $email->writeable()->save();
    }

    /**
     * @param PrimaryEmailUnset $event
     *
     * @return void
     */
    public function onEmailPrimaryUnset(PrimaryEmailUnset $event): void
    {
        $email = $event->email;

        if ($email->user->emails()->count() === 1) {
            abort(422, 'Cannot unset the last primary email');
        }

        $email->is_primary = false;
        $email->writeable()->save();
    }
}
