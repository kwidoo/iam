<?php

namespace App\Resolvers\Organizations;

use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use Kwidoo\Mere\Contracts\Repositories\OrganizationRepository;
use App\Contracts\Resolvers\OrganizationResolver;
use App\Enums\RegistrationFlow;
use InvalidArgumentException;
use RuntimeException;

class ConsoleOrganizationResolver implements OrganizationResolver
{
    protected ?RegistrationFlow $flow = null;

    public function __construct(
        protected OrganizationRepository $repository,
    ) {
        if (!app()->runningInConsole()) {
            throw new RuntimeException('This resolver is only for console commands.');
        }
    }

    /**
     * @param string|null $name
     * @return \App\Models\Organization|null
     * @throws InvalidArgumentException
     */
    public function resolve(?string $name = null): ?OrganizationInterface
    {
        if ($this->flow === RegistrationFlow::MAIN_ONLY) {
            return $this->repository->getMainOrganization();
        }
        if ($this->flow === RegistrationFlow::USER_JOINS_USER_ORG && $name !== null) {
            return $this->repository
                ->where('slug', $name)
                ->orWhere('id', $name)
                ->first();
        }

        return null;
    }

    /**
     * @param RegistrationFlow $flow
     *
     * @return self
     */
    public function forFlow(RegistrationFlow $flow): self
    {
        $this->flow = $flow;

        return $this;
    }
}
