<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'otp' => [
        'model'  => \Kwidoo\MultiAuth\Models\OTP::class,
        'length' => 6,
        'ttl'    => 5,
    ],
    'strategies' => [
        'twilio' => [
            'class'     => \Kwidoo\MultiAuth\Services\TwilioService::class,
            'strategy'  => \Kwidoo\MultiAuth\Services\OTPStrategy::class,
            'resolver' => \App\Services\IamUserResolver::class,

        ],
        'email' => [
            'class'     => \Kwidoo\MultiAuth\Services\EmailService::class,
            'strategy'  => \Kwidoo\MultiAuth\Services\OTPStrategy::class,
            'resolver' =>  \App\Services\IamUserResolver::class,

        ],
        'qr' => [
            'class'     => \Kwidoo\QrLogin\Services\QrService::class,
            'strategy'  => \Kwidoo\MultiAuth\Services\OTPStrategy::class,
            'resolver' => \App\Services\IamUserResolver::class,
        ],

        // 'telegram' => [
        //     'class'     => \Kwidoo\MultiAuth\Services\TelegramService::class,
        //     'strategy'  => \Kwidoo\MultiAuth\Services\OTPStrategy::class,
        //     'resolver' => \Kwidoo\MultiAuth\Resolvers\GeneralUserResolver::class,
        // ],
    ],
];
