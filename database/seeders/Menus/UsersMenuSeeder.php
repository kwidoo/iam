<?php

namespace Database\Seeders\Menus;

use Illuminate\Database\Seeder;
use Kwidoo\Mere\Models\MenuItem;
use Kwidoo\Mere\Data\MenuConfigurationData;
use Kwidoo\Mere\Data\MetaData;
use Kwidoo\Mere\Data\FieldDefinitionData;
use Kwidoo\Mere\Data\RoleConfigurationData;
use Kwidoo\Mere\Data\ValidationRulesData;

class UsersMenuSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            new FieldDefinitionData(
                key: 'id',
                label: 'ID',
                type: 'uuid',
                editable: false,
                visible: true,
                sortable: true
            ),
            new FieldDefinitionData(
                key: 'password',
                label: 'Password',
                type: 'password',
                editable: true,
                visible: false
            ),
            new FieldDefinitionData(
                key: 'created_at',
                label: 'Created At',
                type: 'datetime',
                editable: false,
                visible: true,
                sortable: true
            ),
            new FieldDefinitionData(
                key: 'updated_at',
                label: 'Updated At',
                type: 'datetime',
                editable: false,
                visible: false
            ),
            new FieldDefinitionData(
                key: 'deleted_at',
                label: 'Deleted At',
                type: 'datetime',
                editable: false,
                visible: false
            ),
        ];

        $rules = new ValidationRulesData([
            'create' => [
                'password' => config('iam.use_password', true)
                    ? 'required|string|min:8'
                    : 'nullable',
            ],
            'update' => [
                'password' => config('iam.use_password', true)
                    ? 'nullable|string|min:8'
                    : 'nullable',
            ],
        ]);

        $config = new MenuConfigurationData(
            version: 'v1',
            meta: new MetaData(
                resource: 'User',
                translationKey: 'users',
                apiEndpoint: '/api/users'
            ),
            default: new RoleConfigurationData(
                fields: $fields,
                actions: ['create' => true, 'update' => true, 'delete' => true],
                rules: $rules
            ),
            roles: null
        );

        MenuItem::updateOrCreate(
            ['name' => 'users'],
            [
                'path' => 'users',
                'component' => 'de',
                'props' => $config
            ]
        );
    }
}
