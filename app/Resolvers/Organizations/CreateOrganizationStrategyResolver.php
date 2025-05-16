<?php

namespace App\Resolvers\Organizations;

use App\Contracts\Resolvers\StrategyResolver;
use App\Data\Organizations\OrganizationConfigData;
use App\Data\Registration\RegistrationConfigData;
use Illuminate\Contracts\Container\Container;
use RuntimeException;

class CreateOrganizationStrategyResolver implements StrategyResolver
{
    protected ?OrganizationConfigData $config;

    public function __construct(
        protected Container $container,
        protected array $flowServices,
        protected array $modeServices,
        ?RegistrationConfigData $config = null,
    ) {
        $this->config = $config;
    }

    public function setConfig(OrganizationConfigData $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function resolve(string $type, mixed $service): object
    {
        if (!$this->config) {
            throw new RuntimeException("Registration configuration is not set.");
        }

        // Validate that the property exists
        $property = $type . 'Services';

        if (!property_exists($this, $property)) {
            throw new RuntimeException("Unknown service type: {$type}");
        }

        $enum = $this->config->$type ?? null;
        if (!$enum) {
            throw new RuntimeException("Missing enum value for [{$type}] in RegistrationConfigData.");
        }

        $enumValue = $enum->value;
        $serviceMap = $this->$property;

        if (!isset($serviceMap[$enumValue])) {
            throw new RuntimeException("Service not found for type [{$type}] and value [{$enumValue}].");
        }

        return $this->container->make($serviceMap[$enumValue], ['service' => $service]);
    }
}
