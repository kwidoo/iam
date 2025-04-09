<?php

use App\Enums\RegistrationFlow;

return [
    /*
    |--------------------------------------------------------------------------
    | Registration Method Configuration
    |--------------------------------------------------------------------------
    |
    | These settings control which registration methods are available to users.
    |
    */
    'allow_email' => true,
    'allow_phone' => true,
    'allow_otp' => true,
    'should_verify' => true,

    /*
    |--------------------------------------------------------------------------
    | OTP Configuration
    |--------------------------------------------------------------------------
    |
    | These settings control the behavior of OTP-based registration.
    |
    */
    'otp' => [
        'length' => 6,
        'ttl'    => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Configuration
    |--------------------------------------------------------------------------
    |
    | These settings provide default values for various registration aspects.
    |
    */
    'defaults' => [
        // Default application name used in organization creation
        'name' => env('APP_NAME', 'MyApp'),

        // Default registration strategy when none is specified
        'registration_strategy' => RegistrationFlow::USER_CREATES_ORG,

        // Whether to enforce invite codes for organization registration
        'enforce_invite_code' => false,

        // Whether to allow duplicate identity (email/phone) across organizations
        'allow_duplicate_identity_across_orgs' => env('IAM_ALLOW_DUPLICATE_IDENTITY', false),
    ],
];
