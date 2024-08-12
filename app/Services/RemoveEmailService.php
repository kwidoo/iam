<?php

namespace App\Services;

use App\Contracts\Aggregates\UserAggregate;
use App\Contracts\Services\RemoveEmailService as RemoveEmailServiceContract;
use App\Data\Update\EmailData;
use App\Models\Email;
use Illuminate\Support\Facades\DB;

class RemoveEmailService implements RemoveEmailServiceContract
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
                if ($email->user === null || $email->user_uuid === null) {
                    abort(422, 'Email has no user');
                }

                if ($email->user->emails()->count() === 1) {
                    abort(422, 'Cannot remove last email');
                }

                if ($email->is_primary && $email->user->emails()->isVerified()->count() === 1) {
                    abort(422, 'Cannot remove the last verified email');
                }

                $shouldSet = null;
                if ($email->is_primary) {
                    $shouldSet = $email->user->emails()->where('uuid', '!=', $email->uuid)->isVerified()->firstOrFail();
                    $this->aggregate->retrieve($email->user->uuid)
                        ->unsetPrimaryEmail(new EmailData(
                            emailValue: $email,
                            referenceId: $referenceId,
                        ))
                        ->setPrimaryEmail(new EmailData(
                            emailValue: $shouldSet,
                            referenceId: $referenceId,
                        ))
                        ->persist();
                }

                $this->aggregate->retrieve($email->user->uuid)
                    ->removeEmail(new EmailData(
                        emailValue: $email,
                        referenceId: $referenceId,
                    ))
                    ->persist();
            });
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
