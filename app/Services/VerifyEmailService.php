<?php

namespace App\Services;

use App\Contracts\Aggregates\UserAggregate;
use App\Models\Email;
use App\Contracts\Services\VerifyEmailService as VerifyEmailServiceContract;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VerifyEmailService implements VerifyEmailServiceContract
{
    /**
     * @param UserAggregate $aggregate
     */
    public function __construct(protected UserAggregate $aggregate)
    {
        //
    }

    public function __invoke(Email $email): void
    {
        $referenceId = Str::uuid()->toString();
        try {
            DB::transaction(function () use ($email, $referenceId) {

                if ($email->user === null || $email->user_uuid === null) {
                    throw new Exception('Email has no user');
                }

                $this->aggregate
                    ->retrieve($email->user_uuid)
                    ->verifyEmail(
                        email: $email,
                        referenceId: $referenceId,
                    )
                    ->persist();
            });
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
