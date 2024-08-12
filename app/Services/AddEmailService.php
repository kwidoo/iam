<?php

namespace App\Services;

use App\Contracts\Aggregates\UserAggregate;
use App\Contracts\Services\AddEmailService as AddEmailServiceContract;
use App\Data\Create\EmailData;
use App\Data\Update\EmailData as UpdateEmailData;
use App\Exceptions\EmailCreationFailed;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AddEmailService implements AddEmailServiceContract
{
    /**
     * Create a new instance of the AddEmailService.
     *
     * @param UserAggregate $aggregate The CreateEmail implementation.
     */
    public function __construct(protected UserAggregate $aggregate)
    {
        //
    }

    /**
     * Add an email to a user.
     *
     * @param User $user The user to add the email to.
     * @param string $email The email to add.
     * @param string|null $referenceId The reference ID for the email.
     * @return void
     * @throws EmailCreationFailed If the email creation fails.
     */
    public function __invoke(User $user, string $email, string $referenceId = null): void
    {
        try {
            DB::transaction(function () use ($user, $email, $referenceId) {
                $shouldDelete = null;

                $user->load('emails');
                if ($user->emails->count() === 1 && !$user->hasVerifiedEmail()) {
                    $shouldDelete = $user->email;
                }

                if ($referenceId === null) {
                    $referenceId = Str::uuid()->toString();
                }
                $this->aggregate
                    ->retrieve($user->uuid)
                    ->createEmail(new EmailData(
                        uuid: Str::uuid()->toString(),
                        email: $email,
                        userUuid: $user->uuid,
                        referenceId: $referenceId,

                    ))->persist();

                if ($shouldDelete) {
                    $this->aggregate
                        ->retrieve($user->uuid)
                        ->removeEmail(new UpdateEmailData(
                            emailValue: $shouldDelete,
                            referenceId: $referenceId
                        ))
                        ->persist();
                }

                $this->aggregate
                    ->retrieve($user->uuid)
                    ->updateUserAfterEmailCreated([
                        'email' => $email,
                        'user_uuid' => $user->uuid,
                        'reference_id' => $referenceId,
                    ])->persist();
            });
        } catch (\Exception $e) {
            $message = config('app.debug') ? 'Email creation failed: ' . $e->getMessage() : 'User creation failed';
            throw new EmailCreationFailed($message, 422, $e);
        }
    }
}
