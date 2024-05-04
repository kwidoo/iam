<?php

namespace App\Projectors;

use App\Events\RuleList\RuleCreated;
use App\Events\RuleList\RuleGroupAttached;
use App\Events\RuleList\RuleToGroupAdded;
use App\Events\RuleList\RuleToGroupAttached;
use App\Models\Rule;
use App\Models\RuleGroup;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class RuleProjector extends Projector
{
    public function onRuleCreated(RuleCreated $event)
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

    public function onRuleToGroupAdded(RuleToGroupAttached $event)
    {
        $rule = Rule::find($event->data['ruleUuid']);
        $rule->rule_group_uuid = $event->data['ruleGroupUuid'];
        $rule->writeable()->save();
    }
}
