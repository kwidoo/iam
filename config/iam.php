<?php

return [
    'read_connection' => env('IAM_READ_CONNECTION', 'mariadb'),   // should be defined in config/database.php
    'write_connection' => env('IAM_WRITE_CONNECTION', 'mariadb'),

    'user_read_model' => env('IAM_USER_READ_MODEL', App\Models\UserProfile::class),
    'user_write_model' => env('IAM_USER_WRITE_MODEL', App\Models\User::class),

    'authentication_data' => [
        'email' => [
            'read_model' => env('IAM_EMAIL_READ_MODEL', App\Models\Email::class),
            'write_model' => env('IAM_EMAIL_WRITE_MODEL', App\Models\Email::class),
        ],
        'phone' => [
            'read_model' => env('IAM_PHONE_READ_MODEL', App\Models\Phone::class),
            'write_model' => env('IAM_PHONE_WRITE_MODEL', App\Models\Phone::class),
        ],
    ],

    /** set false  */
    'use_password' => env('IAM_USE_PASSWORD', true),
    'with_password_confirmation' => env('IAM_WITH_PASSWORD_CONFIRMATION', env('IAM_USE_PASSWORD', true)),
    'with_organization' => env('IAM_WITH_ORGANIZATION', true),
];
