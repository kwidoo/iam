<?php

use App\Enums\RegistrationFlow;

return [
    'allow_email' => true,
    'allow_phone' => true,
    'allow_otp' => true,
    'should_verify' => true,
    'otp' => [
        'length' => 6,
        'ttl'    => 5,
    ],
    'defaults' => [
        'name' => env('APP_NAME', 'MyApp'),
        'registration_strategy' => RegistrationFlow::USER_CREATES_ORG,
        'enforce_invite_code' => false,
        'allow_duplicate_identity_across_orgs' => env('IAM_ALLOW_DUPLICATE_IDENTITY', false),


    ],
];
