<?php

namespace App\Services;

use App\Contracts\CreateEmail;
use App\Models\Email;
use App\Contracts\VerifyEmailService as VerifyEmailServiceContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VerifyEmailService implements VerifyEmailServiceContract
{
    public function __construct(protected CreateEmail $aggregate)
    {
        //
    }

    public function __invoke(Email $email)
    {
        $referenceId = Str::uuid()->toString();
        try {
            DB::transaction(function () use ($email, $referenceId) {

                $this->aggregate
                    ->retrieve($email->user_uuid)
                    ->verifyEmail($email, $referenceId)
                    ->persist($referenceId);
            });
        } catch (\Exception $e) {
            throw $e;
            abort(422, 'Email verification failed');
        }
    }
}
