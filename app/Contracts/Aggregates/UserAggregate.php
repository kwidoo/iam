<?php

namespace App\Contracts\Aggregates;

use App\Data\Create\EmailData;
use App\Data\Create\OrganizationData;
use App\Data\Create\PhoneData;
use App\Data\Create\UserData;
use App\Data\Create\ProfileData;
use App\Data\Update\EmailData as UpdateEmailData;
use App\Data\Update\UserData as UpdateUserData;
use App\Models\Email;
use App\Models\User;

interface UserAggregate extends Aggregate
{
    public function createUser(UserData $userData): self;
    public function updateUserAfterCreated(UpdateUserData $userData): self;

    public function createProfile(ProfileData $data): self;
    public function createOrganization(OrganizationData $data): self;

    /**
     * @param User $user
     * @param array<string,string> $data
     *
     * @return self
     */
    public function userLoggedIn(User $user, array $data): self;

    /**
     * @param User|null $user
     * @param array<string,string> $data
     *
     * @return self
     */
    public function userLoginFailed(?User $user, array $data): self;

    public function createEmail(EmailData $emailData): self;
    public function unsetPrimaryEmail(UpdateEmailData $emailData): self;
    public function setPrimaryEmail(UpdateEmailData $emailData): self;
    public function removeEmail(UpdateEmailData $emailData): self;
    /**
     *
     * @param array<string,string> $data
     * @return UserAggregate
     */
    public function updateUserAfterEmailCreated(array $data): self;
    public function verifyEmail(Email $email, string $referenceId): self;
    public function sendEmailVerification(Email $email, string $referenceId): self;

    public function createPhone(PhoneData $phoneData): self;
}
