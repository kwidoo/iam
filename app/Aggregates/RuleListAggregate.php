<?php

namespace App\Aggregates;

use App\Contracts\Aggregates\RuleListAggregate as RuleListAggregateContract;
use App\Events\RuleList\EntityAttached;
use App\Events\RuleList\RuleCreated;
use App\Events\RuleList\RuleGroupAttached;
use App\Events\RuleList\RuleGroupCreated;
use App\Events\RuleList\RuleToGroupAttached;
use Illuminate\Database\Eloquent\Model;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class RuleListAggregate extends AggregateRoot implements RuleListAggregateContract
{
    /**
     * Create a new event instance.
     *
     * @param array<string,mixed> $data
     */
    public function createRuleGroup(array $data): self
    {
        $this->recordThat(new RuleGroupCreated($data));

        return $this;
    }

    /**
     * Create a new event instance.
     *
     * @param array<string,mixed> $data
     */
    public function createRule(array $data): self
    {
        $this->recordThat(new RuleCreated($data));

        return $this;
    }

    /**
     * Create a new event instance.
     *
     * @param array<string,mixed> $data
     */
    public function attachRuleToGroup(array $data): self
    {
        $this->recordThat(new RuleToGroupAttached($data));

        return $this;
    }

    /**
     * Create a new event instance.
     *
     * @param array<string,mixed> $data
     */
    public function attachRuleGroup(array $data): self
    {
        $this->recordThat(new RuleGroupAttached($data));

        return $this;
    }

    /**
     * Create a new event instance.
     *
     * @param Model $entity
     * @param string $groupUuid
     */
    public function attachToEntity($entity, string $groupUuid): self
    {
        $this->recordThat(new EntityAttached($entity, $groupUuid));

        return $this;
    }
}
