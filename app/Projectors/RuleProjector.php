<?php

namespace App\Projectors;

use App\Events\RuleList\RuleCreated;
use App\Events\RuleList\RuleToGroupAttached;
use App\Models\Rule;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class RuleProjector extends Projector
{
    /**
     * @param RuleCreated $event
     *
     * @return void
     */
    public function onRuleCreated(RuleCreated $event): void
    {
        $rule = new Rule([
            'uuid' => $event->data['ruleUuid'],
            'description' => $event->data['description'] ?? null,
            'type' => $event->data['type'],
            'conditions' => $event->data['conditions'],
            'operator' => $event->data['operator'] ?? null,
            'order' => $event->data['order'] ?? null,
        ]);

        $rule->writeable()->save();
    }

    /**
     * @param RuleToGroupAttached $event
     *
     * @return void
     */
    public function onRuleToGroupAdded(RuleToGroupAttached $event): void
    {
        /** @var Rule $rule */
        $rule = Rule::findOrFail($event->data['ruleUuid']);
        $rule->rule_group_uuid = $event->data['ruleGroupUuid'];
        $rule->writeable()->save();
    }
}
