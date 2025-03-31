<?php

namespace Database\Seeders\Menus;

use Illuminate\Database\Seeder;
use Kwidoo\Mere\Models\MenuItem;
use Kwidoo\Mere\Data\MenuConfigurationData;
use Kwidoo\Mere\Data\MetaData;
use Kwidoo\Mere\Data\FieldDefinitionData;
use Kwidoo\Mere\Data\RoleConfigurationData;
use Kwidoo\Mere\Data\ValidationRulesData;

class OrganizationUserMenuSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            new FieldDefinitionData(key: 'organization_id', label: 'Organization', type: 'uuid', editable: true, visible: true),
            new FieldDefinitionData(key: 'user_id', label: 'User', type: 'uuid', editable: true, visible: true),
            new FieldDefinitionData(key: 'role', label: 'Role', type: 'select', editable: true, visible: true, options: ['owner', 'admin', 'member']),
            new FieldDefinitionData(key: 'created_at', label: 'Joined At', type: 'datetime', editable: false, visible: true),
            new FieldDefinitionData(key: 'updated_at', label: 'Updated At', type: 'datetime', editable: false, visible: false),
        ];

        $rules = new ValidationRulesData([
            'create' => [
                'organization_id' => 'required|uuid|exists:organizations,id',
                'user_id' => 'required|uuid|exists:users,id',
                'role' => 'required|in:owner,admin,member',
            ],
            'update' => [
                'role' => 'sometimes|in:owner,admin,member',
            ],
        ]);

        $config = new MenuConfigurationData(
            version: 'v1',
            meta: new MetaData(
                resource: 'OrganizationUser',
                translationKey: 'organization_user',
                apiEndpoint: '/api/organization-users'
            ),
            default: new RoleConfigurationData(
                fields: $fields,
                actions: ['create' => true, 'update' => true, 'delete' => true],
                rules: $rules
            ),
            roles: null
        );

        MenuItem::updateOrCreate(
            ['name' => 'organization_user'],
            [
                'path' => 'organization_user',
                'component' => 'de',
                'props' => $config
            ]
        );
    }
}
