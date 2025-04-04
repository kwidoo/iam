<?php

namespace App\Factories;

use App\Authorizers\AuthenticatedRegistrationAuthorizer;
use App\Authorizers\RegistrationAuthorizer;
use Kwidoo\Mere\Contracts\Authorizer;
use Kwidoo\Mere\Contracts\AuthorizerFactory as AuthorizerFactoryContract;
use Kwidoo\Mere\Factories\DefaultAuthorizer;
use Kwidoo\Mere\Resolvers\AuthAwareResolver;

class AuthorizerFactory implements AuthorizerFactoryContract
{
    public function resolve(string $context): Authorizer
    {
        return match ($context) {
            'registration' => app()->make(AuthAwareResolver::class)->resolve(
                RegistrationAuthorizer::class,
                AuthenticatedRegistrationAuthorizer::class,
                'api'
            ),
            'resource' => app()->make(DefaultAuthorizer::class),
            default => app()->make(DefaultAuthorizer::class),
        };
    }
}
