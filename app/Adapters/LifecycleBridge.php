<?php

namespace App\Adapters;

use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle as NewLifecycle;
use Kwidoo\Mere\Contracts\Lifecycle as OldLifecycle;
use Kwidoo\Lifecycle\Data\LifecycleContextData;
use Kwidoo\Lifecycle\Data\LifecycleOptionsData;
use Kwidoo\Mere\Data\LifecycleContext;

/**
 * Bridge adapter to facilitate migration from Kwidoo\Mere\Contracts\Lifecycle
 * to Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle.
 *
 * This class implements the old Lifecycle interface but delegates to the new one,
 * adapting method signatures and functionality as needed.
 */
class LifecycleBridge implements OldLifecycle
{
    /**
     * @var LifecycleOptionsData
     */
    protected LifecycleOptionsData $options;

    /**
     * @var LifecycleContext|null
     */
    protected ?LifecycleContext $context = null;

    /**
     * @param NewLifecycle $lifecycle
     */
    public function __construct(
        protected NewLifecycle $lifecycle
    ) {
        $this->options = new LifecycleOptionsData();
    }

    /**
     * Run an operation within the lifecycle management system.
     *
     * @param string $action The action being performed (e.g., 'create', 'update')
     * @param string $resource The resource type being acted upon
     * @param mixed $context Additional context data for the operation
     * @param callable $callback The operation to execute within the lifecycle
     * @return mixed The result of executing the callback
     */
    public function run(string $action, string $resource, mixed $context, callable $callback): mixed
    {
        // Create lifecycle context data for new implementation
        $lifecycleContext = new LifecycleContextData(
            action: $action,
            resource: $resource,
            context: $context
        );

        // Execute with the new lifecycle implementation
        $result = $this->lifecycle->run(
            $lifecycleContext,
            function ($data) use ($callback) {
                return $callback();
            },
            $this->options
        );

        // Reset options for next call
        $this->options = new LifecycleOptionsData();

        return $result;
    }

    /**
     * Disable event dispatching for the next operation.
     *
     * @return static The same instance for method chaining
     */
    public function withoutEvents(): static
    {
        $this->options = $this->options->withoutEvents();
        return $this;
    }

    /**
     * Disable transaction wrapping for the next operation.
     *
     * @return static The same instance for method chaining
     */
    public function withoutTrx(): static
    {
        $this->options = $this->options->withoutTrx();
        return $this;
    }

    /**
     * Disable authorization checks for the next operation.
     *
     * @return static The same instance for method chaining
     */
    public function withoutAuth(): static
    {
        $this->options = $this->options->withoutAuth();
        return $this;
    }

    /**
     * Disable aggregate recording for the next operation.
     * This prevents the operation from being recorded in event sourcing aggregates.
     *
     * @return static The same instance for method chaining
     */
    public function withoutAggregateRecording(): static
    {
        // Update options to disable aggregate recording
        $this->options = $this->options->withoutAggregateRecording();
        return $this;
    }

    /**
     * Retrieve the lifecycle context associated with the current instance.
     *
     * @return LifecycleContext|null The context object providing lifecycle-related information.
     */
    public function context(): ?LifecycleContext
    {
        return $this->context;
    }
}
