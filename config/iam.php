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
];
