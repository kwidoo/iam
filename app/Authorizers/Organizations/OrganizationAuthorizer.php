<?php

namespace App\Authorizers\Organizations;

use App\Enums\OrganizationFlow;
use Exception;
use Kwidoo\Lifecycle\Contracts\Features\Authorizer;
use Spatie\LaravelData\Contracts\BaseData;


class OrganizationAuthorizer implements Authorizer
{
    public function __construct(
        protected MainOrganizationAuthorizer $mainAuth,
        protected OnlyMainOrganizationAuthorizer $onlyMainAuth,
    ) {}

    public function authorize(string $ability, ?BaseData $context = null): void
    {
        if (in_array($context->slug, ['main', 'admin', 'login'])) {
            $this->mainAuth->authorize($ability, $context);
        }

        if ($context->flow === OrganizationFlow::MAIN_ONLY) {
            $this->onlyMainAuth->authorize($ability, $context);
        }
    }
}
