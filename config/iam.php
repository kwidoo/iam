<?php

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
        'registration_strategy' => 'main_only',
        'registration_mode'     => 'invite_only',

    ],
];
