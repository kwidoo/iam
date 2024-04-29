<?php

namespace App\Services;

use App\Aggregates\UserAggregate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Str;

class AddEmailService
{
    /**
     * @param array $data
     *
     * @return void
     */
    public static function addEmail(User $user, string $email, string $referenceId = null)
    {
        try {
            DB::transaction(function () use ($user, $email, $referenceId) {
                $shouldDelete = null;

                $user->load('emails');
                if ($user->emails->count() === 1 && !$user->hasVerifiedEmail()) {
                    $shouldDelete = $user->email;
                }

                $aggregate = (new UserAggregate)->retrieve($user->uuid);
                $aggregate->createEmail([
                    'email_uuid' => Str::uuid()->toString(),
                    'email' => $email,
                    'user_uuid' => $user->uuid,
                    'reference_id' => $referenceId
                ]);
                $aggregate->persist($referenceId);

                if ($shouldDelete) {
                    $aggregate->removeEmail($shouldDelete, $referenceId);
                }
                $aggregate->updateUserAfterEmailCreated([
                    'email_uuid' => Str::uuid()->toString(),
                    'email' => $email,
                    'user_uuid' => $user->uuid,
                    'reference_id' => $referenceId
                ]);
                $aggregate->persist($referenceId);
            });
        } catch (\Exception $e) {
            throw $e;
            abort(422, 'Email creation failed');
        }
    }
}
