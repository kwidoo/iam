<?php

namespace App\Enums;

enum RegistrationFlow: string
{
    case MAIN_ONLY = 'main_only';
    case USER_CREATES_ORG = 'user_creates_org';
    case USER_JOINS_USER_ORG = 'user_joins_user_org';
    case INITIAL_BOOTSTRAP = 'initial_bootstrap';


    public function isMainOnly(): bool
    {
        return $this === self::MAIN_ONLY;
    }

    public function isUserCreatesOrg(): bool
    {
        return $this === self::USER_CREATES_ORG;
    }

    public function isUserJoinsUserOrg(): bool
    {
        return $this === self::USER_JOINS_USER_ORG;
    }

    public function isInitialBootstrap(): bool
    {
        return $this === self::INITIAL_BOOTSTRAP;
    }
}
