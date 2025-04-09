<?php

/*
 * You can place your custom package configuration in here.
 */

use App\Aggregates\RegistrationAggregate;

return [
    'menu_model' => \Kwidoo\Mere\Models\MenuItem::class,

    'resources' => [
        'microservices' => \App\Services\MicroserviceService::class,
        'roles' => \App\Services\RoleService::class,
        'permissions' => \App\Services\PermissionService::class,
        'organizations' => \App\Services\OrganizationService::class,
        'profiles' => \App\Services\ProfileService::class,
        'users' => \App\Services\UserService::class,

    ],
    'aggregates' => [
        'recorded' => [
            'registration' => [
                'registerNewUser' => RegistrationAggregate::class,
                'create' => RegistrationAggregate::class,
            ],
            'identity' => [
                'createIdentity' => RegistrationAggregate::class,
            ],
            'profile' => [
                'create' => RegistrationAggregate::class,
            ],
            'organization' => [
                'create' => RegistrationAggregate::class,
            ],
            'contact' => [
                'createContact' => RegistrationAggregate::class,
            ],
            'role' => [
                'create' => RegistrationAggregate::class,
            ],
            'permission' => [
                'create' => RegistrationAggregate::class,
                'assign' => RegistrationAggregate::class,
            ],
            'menu' => ['viewAny' => RegistrationAggregate::class],
            'user' => ['viewAny' => RegistrationAggregate::class],
        ],
    ],
];
