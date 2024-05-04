<?php

namespace App\Services;

use App\Aggregates\RuleListAggregate;
use Illuminate\Database\Eloquent\Model;
use Str;

class AclService
{
    public function __construct(protected RuleListAggregate $aggregate)
    {
        //
    }

    public function createOwnerRule($entity)
    {
        if (!$entity instanceof Model) {
            throw new \InvalidArgumentException('Entity must be an instance of Illuminate\Database\Eloquent\Model');
        }
        $rootUuid = Str::uuid()->toString();
        $ruleUuid = Str::uuid()->toString();

        $this->aggregate->retrieve($rootUuid)->createRuleGroup([
            'ruleGroupUuid' => $rootUuid,
            'description' => 'Root rule group for ' . $entity->getMorphClass(),
            'order' => 0,
        ])->createRule([
            'ruleUuid' => $ruleUuid,
            'description' => 'Owner rule for ' . $entity->getMorphClass(),
            'type' => 'owner',
            'order' => 0,
            'conditions' => [],
        ])->attachRuleToGroup([
            'ruleUuid' => $ruleUuid,
            'ruleGroupUuid' => $rootUuid,
        ])->attachToEntity($entity, $rootUuid)->persist();
    }
}
