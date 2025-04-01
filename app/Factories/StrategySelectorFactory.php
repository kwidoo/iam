<?php

namespace App\Factories;

use RuntimeException;

class StrategySelectorFactory
{
    /**
     * @param array<string, class-string> $strategies
     */
    public function __construct(protected array $strategies) {}

    /**
     * @template TStrategy
     * @param string $key
     * @param mixed $service
     * @return TStrategy
     */
    public function resolve(string $key, mixed $service): object
    {
        if (!isset($this->strategies[$key])) {
            throw new RuntimeException("Strategy not found for key: {$key}");
        }

        return app()->make($this->strategies[$key], ['service' => $service]);
    }
}
