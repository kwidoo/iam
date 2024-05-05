<?php

namespace Database\Seeders;

use App\Contracts\Services\CreateUserService;
use App\Models\Permission;
use App\Models\Role;
use App\Enums\RuleAction;
use App\Enums\RuleGroupType;
use App\Enums\RuleOperator;
use App\Models\Rule;
use App\Models\RuleGroup;
use App\Rules\Data\RuleConditionData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('users')->delete();

        $userUuid = Str::uuid()->toString();
        $userService = app(CreateUserService::class);
        $userService([
            'user_uuid' => $userUuid,
            'name' => 'Oleg Pashkovsky',
            'email' => 'oleg@pashkovsky.me',
            'password' => bcrypt('test'),
            'type' => 'admin',
            'reference_id' => Str::uuid()->toString(),
        ]);

        foreach (array_column(RuleGroupType::cases(), 'value') as $type) {
            $ruleGroup = RuleGroup::create([
                'type' => $type,
                'entity_type' => 'organization',
                'entity_uuid' => null,
                'description' => 'User model ' . $type . ' rule group',
            ]);
            $rule = new Rule([
                'rule_group_uuid' => $ruleGroup->refresh()->uuid,
                'action' => RuleAction::allow,
                'conditions' =>
                RuleConditionData::from(
                    comparison: 'equals',
                    subject: 'user_uuid',
                    value: $userUuid,
                ),
                'operator' => RuleOperator::and,
                'order' => 0,
            ]);
            $rule->writeable()->save();
        }

        // $organization = User::find($userUuid)->organizations->first();
        // $profile = $organization->owner_profile;

        // app(AclService::class)->createOwnerRule($organization);
        // app(AclService::class)->createOwnerRule($profile);


        Role::create([
            'name' => 'admin',
            'guard_name' => 'api',
        ]);

        $role = Role::create([
            'name' => 'landlord',
            'guard_name' => 'api',
        ]);

        Role::create([
            'name' => 'tenant',
            'guard_name' => 'api',
        ]);

        $permission = Permission::create([
            'name' => 'create property',
            'guard_name' => 'api',
        ]);

        $role->givePermissionTo($permission);

        $permission = Permission::create([
            'name' => 'remove property',
            'guard_name' => 'api',
        ]);

        $role->givePermissionTo($permission);

        DB::insert('insert into model_has_roles (role_uuid, model_type, model_uuid) values (?, ?, ?)', [$role->uuid, 'user', $userUuid]);
    }
}
