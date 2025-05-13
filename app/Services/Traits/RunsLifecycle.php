<?php

namespace App\Services\Traits;

use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Kwidoo\Lifecycle\Data\LifecycleContextData;
use Kwidoo\Lifecycle\Data\LifecycleOptionsData;
use Spatie\LaravelData\Contracts\BaseData;

trait RunsLifecycle
{
    protected LifecycleOptionsData $options;
    protected Lifecycle $lifecycle;

    public function withLifecycleOptions(LifecycleOptionsData $options): void
    {
        $this->options =  $options;
    }

    protected function runLifecycle(BaseData $context, callable $callback, ?string $overrideAction = null, ?string $overrideResource = null): mixed
    {
        [$action, $resource] = $this->resolveActionAndResource();

        $lcData = new LifecycleContextData(
            action: $overrideAction ?? $action,
            resource: $overrideResource ?? $resource,
            context: $context,
        );

        return $this->lifecycle->run(
            data: $lcData,
            callback: $callback,
            options: $this->options ?? null,
        );
    }

    private function resolveActionAndResource(): array
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $caller = $trace[2]['function'] ?? 'unknownAction';

        $classParts = explode('\\', static::class);
        $serviceName = end($classParts);

        $resource = str_replace(['Default', 'Service'], '', $serviceName);
        $resource = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $resource));
        $resource = rtrim($resource, 's') . 's';

        return [$caller, $resource];
    }
}
