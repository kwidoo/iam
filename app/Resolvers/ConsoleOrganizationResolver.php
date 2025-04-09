<?php

namespace App\Resolvers;

use App\Contracts\Repositories\OrganizationRepository;
use App\Contracts\Resolvers\OrganizationResolver;
use App\Enums\RegistrationFlow;
use App\Models\Organization;
use InvalidArgumentException;
use RuntimeException;

class ConsoleOrganizationResolver implements OrganizationResolver
{
    protected ?RegistrationFlow $flow = null;

    public function __construct(protected OrganizationRepository $repository,)
    {
        if (!app()->runningInConsole()) {
            throw new RuntimeException('This resolver is only for console commands.');
        }
    }

    /**
     *
     * Resolve the organization based on the provided context.
     *
     * @param string|null $name
     * @return Organization|null
     * @throws InvalidArgumentException
     */
    public function resolve(?string $name = null): ?Organization
    {
        dump($this->flow);
        if ($this->flow === RegistrationFlow::MAIN_ONLY) {
            return $this->repository->getMainOrganization();
        }
        if ($this->flow === RegistrationFlow::USER_JOINS_USER_ORG && $name === null) {
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
