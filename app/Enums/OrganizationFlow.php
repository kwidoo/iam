<?php

namespace App\Enums;

/**
 * Enum representing different registration flows in the system.
 * Defines how users can register and their relationship with organizations.
 *
 * @category App\Enums
 * @package  App\Enums
 * @author   John Doe <john.doe@example.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/your-repo
 */
enum OrganizationFlow: string
{
/**
     * User registers without organization association
     */
    case MAIN_ONLY = 'main_only';

/**
     * User creates a new organization during registration
     */
    case USER_CREATES_ORG = 'user_creates_org';

/**
     * User joins an existing organization during registration
     */
    case USER_JOINS_USER_ORG = 'user_joins_user_org';

/**
     * Initial system bootstrap registration
     */
    case INITIAL_BOOTSTRAP = 'initial_bootstrap';

    /**
     * Check if this is a main-only registration flow.
     *
     * @return bool
     */
    public function isMainOnly(): bool
    {
        return $this === self::MAIN_ONLY;
    }

    /**
     * Check if this is a user-creates-org registration flow.
     *
     * @return bool
     */
    public function isUserCreatesOrg(): bool
    {
        return $this === self::USER_CREATES_ORG;
    }

    /**
     * Check if this is a user-joins-org registration flow.
     *
     * @return bool
     */
    public function isUserJoinsUserOrg(): bool
    {
        return $this === self::USER_JOINS_USER_ORG;
    }

    /**
     * Check if this is an initial bootstrap registration flow.
     *
     * @return bool
     */
    public function isInitialBootstrap(): bool
    {
        return $this === self::INITIAL_BOOTSTRAP;
    }
}
