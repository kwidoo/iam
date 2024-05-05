<?php

namespace App\Projectors;

use App\Events\Email\AfterEmailCreated;
use App\Events\Email\EmailCreated;
use App\Events\Email\EmailRemoved;
use App\Events\Email\EmailVerified;
use App\Events\Email\PrimaryEmailSet;
use App\Events\Email\PrimaryEmailUnset;
use App\Models\Email;
use App\Models\UserProfile;
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
        $emailData = $event->data;
        $email = (new Email([
            'uuid' => $emailData->uuid,
            'user_uuid' => $emailData->userUuid,
            'email' => $emailData->email,
            'is_primary' => $emailData->isPrimary,
        ]));
        $email->writeable()->save();
    }

    // /**
    //  * @param VerifyEmail $event
    //  *
    //  * @return void
    //  */
    // public function onVerifyEmailSend(VerifyEmail $event): void
    // {
    //     $email = $event->email;
    //     $email->notify(new VerifyEmailNotification);
    // }

    /**
     * @param EmailVerified $event
     *
     * @return void
     */
    public function onEmailVerified(EmailVerified $event): void
    {
        $email = $event->email;
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
        $email = $event->emailData->email;
        $email->writeable()->delete();
    }

    /**
     * @param PrimaryEmailSet $event
     *
     * @return void
     */
    public function onEmailPrimarySet(PrimaryEmailSet $event): void
    {
        $email = $event->emailData->email;
        $email->is_primary = true;
        $email->writeable()->save();

        $userProfile = UserProfile::whereUuid($email->user->uuid)->firstOrFail();
        $emails = $email->user->emails;

        $userProfile->email = $email->email;
        $userProfile->email_verified_at = $email->email_verified_at;

        if ($emails->count() > 1) {
            $userProfile->emails = $emails->pluck('email')->filter(fn ($needle) => $needle !== $email->email)->toArray();
        }

        $userProfile->save();
    }

    /**
     * @param PrimaryEmailUnset $event
     *
     * @return void
     */
    public function onEmailPrimaryUnset(PrimaryEmailUnset $event): void
    {
        $email = $event->emailData->email;

        if ($email->user->emails()->count() === 1) {
            abort(422, 'Cannot unset the last primary email');
        }

        $email->is_primary = false;
        $email->writeable()->save();
    }

    /**
     * @param AfterEmailCreated $event
     *
     * @return void
     */
    public function onAfterEmailCreated(AfterEmailCreated $event): void
    {
        $emails = Email::whereUserUuid($event->data['user_uuid'])->get();
        $userProfile = UserProfile::whereUuid($event->data['user_uuid'])->firstOrFail();

        if ($emails->count() === 1) {
            $userProfile->email = $event->data['email'];
        }

        if ($emails->count() > 1) {
            $userProfile->emails = $emails->pluck('email')->filter(fn ($email) => $email !== $userProfile->email)->toArray();
        }

        $userProfile->save();
    }
}
