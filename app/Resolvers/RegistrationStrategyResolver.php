<?php

namespace App\Resolvers;

use App\Contracts\Resolvers\StrategyResolver;
use App\Data\RegistrationConfigData;
use RuntimeException;

class RegistrationStrategyResolver implements StrategyResolver
{
    protected ?RegistrationConfigData $config;

    public function __construct(
        protected array $identityStrategies,
        protected array $flowStrategies,
        protected array $profileStrategies,
        protected array $secretStrategies,
        protected array $modeStrategies,
        ?RegistrationConfigData $config = null,
    ) {
        $this->config = $config;
    }

    public function setConfig(RegistrationConfigData $config): void
    {
        $this->config = $config;
    }

    public function resolve(string $type, mixed $service): object
    {
        if (!$this->config) {
            throw new RuntimeException("Registration configuration is not set.");
        }

        // Validate that the property exists
        $property = $type . 'Strategies';

        if (!property_exists($this, $property)) {
            throw new RuntimeException("Unknown strategy type: {$type}");
        }

        $enum = $this->config->$type ?? null;
        if (!$enum) {
            throw new RuntimeException("Missing enum value for [{$type}] in RegistrationConfigData.");
        }

        $enumValue = $enum->value;
        $strategyMap = $this->$property;

        if (!isset($strategyMap[$enumValue])) {
            throw new RuntimeException("Strategy not found for type [{$type}] and value [{$enumValue}].");
        }

        return app()->make($strategyMap[$enumValue], ['service' => $service]);
    }
}
