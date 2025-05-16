<?php

namespace App\Authorizers\Organizations;

use App\Enums\OrganizationFlow;
use Kwidoo\Lifecycle\Contracts\Features\Authorizer;
use Spatie\LaravelData\Contracts\BaseData;
use Exception;

class OnlyMainOrganizationAuthorizer implements Authorizer
{
    /**
     * @param string $ability
     * @param \App\Data\Organizations\OrganizationCreateData|null $context
     *
     * @return void
     */
    public function authorize(string $ability, ?BaseData $context = null): void
    {
        if ($ability === 'create' && $context?->flow !== OrganizationFlow::MAIN_ONLY) {
            throw new Exception("Can't create organization, join existing.");
        }
    }
}
