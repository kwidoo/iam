<?php

namespace App\Authorizers\Organizations;

use App\Enums\OrganizationFlow;
use Kwidoo\Lifecycle\Contracts\Features\Authorizer;
use Spatie\LaravelData\Contracts\BaseData;
use Exception;

class MainOrganizationAuthorizer implements Authorizer
{
    /**
     * @param string $ability
     * @param \App\Data\Organizations\OrganizationCreateData|null $context
     *
     * @return void
     */
    public function authorize(string $ability, ?BaseData $context = null): void
    {
        if ($ability === 'create' && $context?->flow !== OrganizationFlow::INITIAL_BOOTSTRAP && !app()->isRunningInConsole()) {
            throw new Exception("This slug is reserved.");
        }
    }
}
