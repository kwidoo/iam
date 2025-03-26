<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'menu_model' => \Kwidoo\Mere\Models\MenuItem::class,

    'resources' => [
        'microservices' => \App\Services\MicroserviceService::class,
        'roles' => \App\Services\RoleService::class,
        'permissions' => \App\Services\PermissionService::class,
        'organizations' => \App\Services\OrganizationService::class,
        'users' => \App\Services\UserService::class,

    ],
];
