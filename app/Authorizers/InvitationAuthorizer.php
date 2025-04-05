<?php

namespace App\Authorization;

use App\Authorizers\RegistrationAuthorizer;
use Kwidoo\Mere\Contracts\Authorizer;
use Spatie\LaravelData\Contracts\BaseData;


class InvitationAuthorizer implements Authorizer
{
    public function __construct(
        protected RegistrationAuthorizer $baseAuthorizer,
        protected iterable $acceptValidators = [],
        protected iterable $sendValidators = []

    ) {
        $this->acceptValidators = app()->tagged('invite.accept.validators');
        $this->sendValidators = app()->tagged('invite.send.validators');
    }

    public function authorize(string $ability, mixed $resource, BaseData $extra): void
    {
        if ($ability === 'sendInvite') {
            foreach ($this->sendValidators as $validator) {
                $validator->validate($extra);
            }
        }

        if ($ability === 'registerNewUser') {
            foreach ($this->acceptValidators as $validator) {
                $validator->validate($extra);
            }
            $this->baseAuthorizer->authorize($ability, $resource, $extra);
        }
    }
}
