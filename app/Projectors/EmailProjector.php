<?php

namespace App\Projectors;

use App\Events\Email\EmailConfirmed;
use App\Events\Email\EmailCreated;
use App\Events\Email\EmailRemoved;
use App\Events\Email\PrimaryEmailSet;
use App\Events\Emails\PrimaryEmailUnset;
use App\Events\VerifyEmail;
use App\Models\Email;
use App\Models\User;
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
        $data = $event->data;

        $user = User::find($data['user_uuid']);

        $shouldDelete = null;
        if ($user->emails()->count() === 1 && !$user->hasVerifiedEmail()) {
            $shouldDelete = $user->email;
        }

        $email = (new Email($data));
        $email->writeable()->save();
        $email->sendEmailVerificationNotification();

        if ($shouldDelete) {
            $shouldDelete->removeEmail();
        }
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

        if ($email->user->emails()->count() === 1) {
            abort(422, 'Cannot remove the last email');
        }

        if ($email->is_primary) {
            $otherEmail = $email->user->emails()->where('uuid', '!=', $email->uuid)->isVerified()->first();
            if ($otherEmail) {
                $otherEmail->setPrimaryEmail();
            }
        }

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
        if (!$email->hasVerifiedEmail()) {
            abort(422, 'Cannot set an unverified email as primary');
        }

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
        $user = $email->user;

        if ($user->emails()->count() === 1) {
            abort(422, 'Cannot unset the last primary email');
        }

        if (!$email->hasVerifiedEmail()) {
            abort(422, 'Cannot set an unverified email as primary');
        }

        $otherEmail = $email->user->emails()->isPrimary()->first();
        if ($otherEmail) {
            $otherEmail->is_primary = false;
            $otherEmail->writeable()->save();
        }

        $email->setPrimaryEmail();
    }
}
