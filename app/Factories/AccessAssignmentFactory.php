<?php

namespace App\Factories;

use Kwidoo\Mere\Contracts\AccessAssignmentFactory as AccessAssignmentFactoryContract;
use App\Resolvers\GuestRegistrationContextResolver;
use App\Resolvers\AuthenticatedRegistrationContextResolver;
use Kwidoo\Mere\Contracts\AccessAssignmentContextResolver;
use Kwidoo\Mere\Resolvers\AuthAwareResolver;

class AccessAssignmentFactory implements AccessAssignmentFactoryContract
{
    public function resolve(): AccessAssignmentContextResolver
    {
        return app()->make(AuthAwareResolver::class)->resolve(
            GuestRegistrationContextResolver::class,
            AuthenticatedRegistrationContextResolver::class,
            'api'
        );
    }
}
