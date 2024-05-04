<?php

namespace App\Projectors;

use App\Events\RuleList\AttachToEntity;
use App\Events\RuleList\EntityAttached;
use App\Events\RuleList\RuleGroupAttached;
use App\Events\RuleList\RuleGroupCreated;
use App\Models\RuleGroup;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class RuleGroupProjector extends Projector
{
    public function onRuleGroupCreated(RuleGroupCreated $event): void
    {
        $ruleGroup = new RuleGroup([
            'uuid' => $event->data['ruleGroupUuid'],
            'description' => $event->data['description'] ?? null,
            'operator' => $event->data['operator'] ?? null,
            'order' => $event->data['order'] ?? null,
        ]);

        $ruleGroup->save();
    }

    public function onRuleGroupAttached(RuleGroupAttached $event): void
    {
        $ruleGroup = RuleGroup::whereUuid($event->data['uuid'])->firstOrFail();
        $ruleGroup->parent_id = $event->data['parent_id'];
        $ruleGroup->save();
    }

    public function onRuleAttachedToEntity(EntityAttached $event): void
    {
        if ($event->entity instanceof Model) {
            if ($event->entity->rule_groups->count() > 0) {
                throw new Exception('Entity already has a rule group');
            }
            $ruleGroup = RuleGroup::whereUuid($event->groupUuid)->firstOrFail();
            if (!$ruleGroup->isRoot()) {
                throw new Exception('Rule group is not root');
            }

            $event->entity->rule_groups()->attach($ruleGroup);
        }
    }
}
