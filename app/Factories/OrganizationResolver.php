<?php

namespace App\Factories;

use App\Contracts\Repositories\OrganizationRepository;
use App\Enums\RegistrationFlow;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationResolver
{
    public function __construct(
        protected OrganizationRepository $repository,
        protected ConfigurationContext $config,
    ) {}
    public function resolve(?string $inputOrg = null): ?Organization
    {
        $flow = $this->config->determineFlow();

        if ($flow === RegistrationFlow::MAIN_ONLY) {
            return $this->repository->findByField('slug', 'main')->first();
        }

        if ($flow === RegistrationFlow::USER_CREATES_ORG) {
            return null;
        }

        $orgSlug = $inputOrg
            ?? request()->route('organization')
            ?? $this->fromSubdomain(request());

        if (! $orgSlug) {
            return null;
        }

        return $this->repository
            ->where('slug', $orgSlug)
            ->orWhere('id', $orgSlug)
            ->first();
    }

    protected function fromSubdomain(Request $request): ?string
    {
        $host = $request->getHost(); // e.g. org.example.com
        $parts = explode('.', $host);
        if (count($parts) < 3) return null;
        return $parts[0]; // org
    }
}
