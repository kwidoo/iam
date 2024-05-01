<?php

namespace App\Services;

use App\Contracts\AddEmailService as ContractsAddEmailService;
use App\Contracts\CreateEmail;
use App\Exceptions\EmailCreationFailed;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AddEmailService implements ContractsAddEmailService
{
    /**
     * Create a new instance of the AddEmailService.
     *
     * @param CreateEmail $aggregate The CreateEmail implementation.
     */
    public function __construct(protected CreateEmail $aggregate)
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
    public function __invoke(User $user, string $email, string $referenceId = null)
    {
        try {
            DB::transaction(function () use ($user, $email, $referenceId) {
                $shouldDelete = null;

                $user->load('emails');
                if ($user->emails->count() === 1 && !$user->hasVerifiedEmail()) {
                    $shouldDelete = $user->email;
                }

                $this->aggregate
                    ->retrieve($user->uuid)
                    ->createEmail([
                        'email_uuid' => Str::uuid()->toString(),
                        'email' => $email,
                        'user_uuid' => $user->uuid,
                        'reference_id' => $referenceId

                    ])->persist($referenceId);

                if ($shouldDelete) {
                    $this->aggregate
                        ->retrieve($user->uuid)
                        ->removeEmail($shouldDelete, $referenceId)
                        ->persist($referenceId);
                }

                $this->aggregate->retrieve($user->uuid)->updateUserAfterEmailCreated([
                    'email' => $email,
                    'user_uuid' => $user->uuid,
                    'reference_id' => $referenceId
                ])->persist($referenceId);
            });
        } catch (\Exception $e) {
            $message = config('app.debug') ? 'Email creation failed: ' . $e->getMessage() : 'User creation failed';
            throw new EmailCreationFailed($message, 422, $e);
        }
    }
}
