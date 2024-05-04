<?php

namespace App\Aggregates;

use App\Events\RuleList\EntityAttached;
use App\Events\RuleList\RuleCreated;
use App\Events\RuleList\RuleGroupAttached;
use App\Events\RuleList\RuleGroupCreated;

use App\Events\RuleList\RuleToGroupAttached;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class RuleListAggregate extends AggregateRoot
{
    public function createRuleGroup(array $data): self
    {
        $this->recordThat(new RuleGroupCreated($data));

        return $this;
    }

    public function createRule(array $data): self
    {
        $this->recordThat(new RuleCreated($data));

        return $this;
    }

    public function attachRuleToGroup(array $data): self
    {
        $this->recordThat(new RuleToGroupAttached($data));

        return $this;
    }

    public function attachRuleGroup(array $data): self
    {
        $this->recordThat(new RuleGroupAttached($data));

        return $this;
    }

    public function attachToEntity($entity, string $groupUuid): self
    {
        $this->recordThat(new EntityAttached($entity, $groupUuid));

        return $this;
    }
}
