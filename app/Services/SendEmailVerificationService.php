<?php

namespace App\Services;

use App\Contracts\Aggregates\UserAggregate;
use App\Contracts\Services\SendEmailVerificationService as SendEmailVerificationServiceContract;
use App\Models\Email;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SendEmailVerificationService implements SendEmailVerificationServiceContract
{
    /**
     * @param UserAggregate $aggregate public
     */
    public function __construct(protected UserAggregate $aggregate)
    {
        //
    }

    /**
     * @param Email $email
     *
     * @return void
     */
    public function __invoke(Email $email): void
    {
        $referenceId = Str::uuid()->toString();

        try {
            DB::transaction(function () use ($email, $referenceId) {
                $this->aggregate
                    ->retrieve($email->user_uuid)
                    ->sendEmailVerification($email, $referenceId)
                    ->persist();
            });
        } catch (Exception $e) {
            throw $e;
        }
    }
}
