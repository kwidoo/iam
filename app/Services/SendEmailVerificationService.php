<?php

namespace App\Services;

use App\Contracts\CreateEmail;
use App\Contracts\SendEmailVerificationService as ContractsSendEmailVerificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SendEmailVerificationService implements ContractsSendEmailVerificationService
{
    public function __construct(public CreateEmail $aggregate)
    {
        //
    }

    public function __invoke($email)
    {
        $referenceId = Str::uuid()->toString();

        try {
            DB::transaction(function () use ($email, $referenceId) {
                $this->aggregate
                    ->retrieve($email->user_uuid)
                    ->sendEmailVerification($email, $referenceId)
                    ->persist($referenceId);
            });
        } catch (\Exception $e) {
            throw $e;
            abort(422, 'Email verification failed');
        }
    }
}
