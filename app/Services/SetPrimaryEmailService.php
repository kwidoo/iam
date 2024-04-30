<?php

namespace  App\Services;

use App\Contracts\CreateEmail;
use App\Contracts\SetPrimaryEmailService as ContractsSetPrimaryEmailService;
use App\Models\Email;
use Illuminate\Support\Facades\DB;

class SetPrimaryEmailService implements ContractsSetPrimaryEmailService
{
    public function __construct(protected CreateEmail $aggregate)
    {
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function __invoke(Email $email, string $referenceId = null)
    {
        try {
            DB::transaction(function () use ($email, $referenceId) {
                if (!$email->hasVerifiedEmail()) {
                    abort(422, 'Cannot set an unverified email as primary');
                }

                $otherEmail = $email->user->emails()->isPrimary()->first();
                if ($otherEmail) {
                    $this->aggregate
                        ->retrieve($email->user->uuid)
                        ->unsetPrimaryEmail($otherEmail, $referenceId)
                        ->persist($referenceId);
                }

                $this->aggregate
                    ->retrieve($email->user->uuid)
                    ->setPrimaryEmail($email, $referenceId)
                    ->persist($referenceId);
            });
        } catch (\Exception $e) {
            throw $e;
            abort(422, 'Primary email setting failed');
        }
    }
}
