<?php

namespace Database\Seeders;

use App\Aggregates\RuleGroupAggregate;
use App\Aggregates\RuleListAggregate;
use App\Models\RuleGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AccessControlSeeder extends Seeder
{
    public function run()
    {
        $rootGroupUuid = $this->generateUuid(); // This is equivalent to the old listUuid

        $groupUuids = [
            $this->generateUuid(),
            $this->generateUuid()
        ];

        $ruleDetails = [
            ['User is the owner of the property', 'owner', [], 'or'],
            ['User has permission to create properties', 'permission', ['permission' => 'create property'], null],
            ['Current month is May', 'time_condition', ['month' => 'May'], 'AND'],
            ['Property country must be Latvia', 'field_condition', ['field' => 'country', 'value' => 'Latvia'], null]
        ];

        $ruleUuids = $this->generateMultipleUuids(count($ruleDetails));

        $rootAggregate = RuleListAggregate::retrieve($rootGroupUuid)
            ->createRuleGroup([
                'ruleGroupUuid' => $rootGroupUuid,
                'description' => 'Property creation rules',
            ])->persist();

        $rootAggregate = RuleListAggregate::retrieve($rootGroupUuid);
        foreach ($groupUuids as $index => $groupUuid) {
            $rootAggregate->createRuleGroup([
                'ruleGroupUuid' => $groupUuid,
                'description' => $index == 0 ? 'Owner and permission rules' : 'Time and field condition rules',
                'order' => $index,
                'operator' => 'AND',
            ])->attachRuleGroup([
                'uuid' => $groupUuid,
                'parent_id' => RuleGroup::whereUuid($rootGroupUuid)->firstOrFail()->id,
            ])->persist();

            $ruleLimit = $index == 0 ? 2 : 4;
            for ($i = $index * 2; $i < $ruleLimit; $i++) {
                $rule = $ruleDetails[$i];
                $rootAggregate->createRule([
                    'ruleUuid' => $ruleUuids[$i],
                    'description' => $rule[0],
                    'type' => $rule[1],
                    'conditions' => json_encode($rule[2]),
                    'ruleGroupUuid' => $groupUuid,
                    'order' => $i % 2, // Each group starts order from 0
                    'operator' => $rule[3],
                ])->attachRuleToGroup([
                    'ruleUuid' => $ruleUuids[$i],
                    'ruleGroupUuid' => $groupUuid,
                ]);
            }
        }

        $rootAggregate->persist();
    }

    private function generateUuid()
    {
        return Str::uuid()->toString();
    }

    private function generateMultipleUuids($count)
    {
        return array_map(function () {
            return $this->generateUuid();
        }, range(1, $count));
    }
}
