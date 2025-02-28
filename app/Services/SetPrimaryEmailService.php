<?php

namespace  App\Services;

use App\Contracts\Aggregates\UserAggregate;
use App\Contracts\Services\SetPrimaryEmailService as SetPrimaryEmailServiceContract;
use App\Data\Update\EmailData;
use App\Models\Email;
use Exception;
use Illuminate\Support\Facades\DB;

class SetPrimaryEmailService implements SetPrimaryEmailServiceContract
{
    public function __construct(protected UserAggregate $aggregate)
    {
    }

    /**
     * @param Email $email
     * @param string $referenceId
     *
     * @return void
     */
    public function __invoke(Email $email, string $referenceId): void
    {
        try {
            DB::transaction(function () use ($email, $referenceId) {
                if (!$email->hasVerifiedEmail()) {
                    throw new Exception('Cannot set an unverified email as primary');
                }

                if ($email->user === null || $email->user_uuid === null) {
                    throw new Exception('Email has no user');
                }

                $otherEmail = $email->user->emails()->isPrimary()->first();
                if ($otherEmail) {
                    $this->aggregate
                        ->retrieve($email->user_uuid)
                        ->unsetPrimaryEmail(new EmailData(
                            emailValue: $otherEmail,
                            referenceId: $referenceId,
                        ))
                        ->persist();
                }

                $this->aggregate
                    ->retrieve($email->user_uuid)
                    ->setPrimaryEmail(new EmailData(
                        emailValue: $email,
                        referenceId: $referenceId,
                    ))
                    ->persist();
            });
        } catch (Exception $e) {
            throw $e;
        }
    }
}
