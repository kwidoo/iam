<?php

namespace App\Reactors;

use App\Events\Email\EmailCreated;
use App\Events\Email\VerifyEmail;
use App\Models\Email;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class EmailReactor extends Reactor implements ShouldQueue
{
    public function onEmailCreated(EmailCreated $event): void
    {
        $email = Email::whereEmail($event->data['email'])->firstOrFail();
        /** @var Email $email */
        $email->sendEmailVerificationNotification();
    }

    /**
     * @param VerifyEmail $event
     *
     * @return void
     */
    public function onVerifyEmail(VerifyEmail $event): void
    {
        $event->email->sendEmailVerificationNotification();
    }
}
