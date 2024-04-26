<?php

namespace App\Services;

use App\Aggregates\UserAggregate;
use App\Models\Email;
use Illuminate\Support\Facades\DB;

class RemoveEmailService
{
    /**
     * @param array $data
     *
     * @return void
     */
    public static function removeEmail(Email $email, string $referenceId = null)
    {
        try {
            DB::transaction(function () use ($email, $referenceId) {
                if ($email->user->emails()->count() === 1) {
                    abort(422, 'Cannot remove last email');
                }

                if ($email->is_primary && $email->user->emails()->isVerified()->count() === 1) {
                    abort(422, 'Cannot remove the last verified email');
                }

                $shouldSet = null;
                if ($email->is_primary) {
                    $shouldSet = $email->user->emails()->where('uuid', '!=', $email->uuid)->isVerified()->firstOrFail();
                    if ($shouldSet) {
                        (new UserAggregate)->retrieve($email->user->uuid)
                            ->unsetPrimaryEmail($email, $referenceId)
                            ->setPrimaryEmail($shouldSet, $referenceId)
                            ->persist($referenceId);
                    }
                }

                (new UserAggregate)->retrieve($email->user->uuid)
                    ->removeEmail($email, $referenceId)
                    ->persist($referenceId);
            });
        } catch (\Exception $e) {
            throw $e;
            abort(422, 'Email removal failed');
        }
    }
}
