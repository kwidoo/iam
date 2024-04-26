<?php

namespace  App\Services;

use App\Aggregates\UserAggregate;
use App\Models\Email;
use Illuminate\Support\Facades\DB;

class SetPrimaryEmailService
{
    /**
     * @param array $data
     *
     * @return void
     */
    public static function setPrimaryEmail(Email $email, string $referenceId = null)
    {
        try {
            DB::transaction(function () use ($email, $referenceId) {
                if (!$email->hasVerifiedEmail()) {
                    abort(422, 'Cannot set an unverified email as primary');
                }

                $aggregate = (new UserAggregate)->retrieve($email->user->uuid);
                $otherEmail = $email->user->emails()->isPrimary()->first();
                if ($otherEmail) {
                    $aggregate->unsetPrimaryEmail($otherEmail, $referenceId);
                }

                $aggregate->setPrimaryEmail($email, $referenceId);
                $aggregate->persist($referenceId);
            });
        } catch (\Exception $e) {
            throw $e;
            abort(422, 'Primary email setting failed');
        }
    }
}
