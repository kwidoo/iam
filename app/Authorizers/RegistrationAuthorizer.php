<?php

namespace App\Authorizers;

use App\Data\RegistrationData;
use Kwidoo\Mere\Contracts\Authorizer;
use Spatie\LaravelData\Contracts\BaseData;
use InvalidArgumentException;

class RegistrationAuthorizer implements Authorizer
{
    public function __construct(
        protected iterable $validators = []
    ) {
        $this->validators = app()->tagged('registration.validators');
    }

    public function authorize(string $ability, mixed $resource, BaseData $extra): void
    {
        if ($ability !== 'registerNewUser' || !in_array($resource,  ['registration', 'registration-invite']) || !($extra instanceof RegistrationData)) {
            throw new InvalidArgumentException('Invalid ability or resource type.');
        }

        foreach ($this->validators as $validator) {
            $validator->validate($extra);
        }
    }
}
