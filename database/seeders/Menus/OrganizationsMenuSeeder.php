<?php

namespace Database\Seeders\Menus;

use Illuminate\Database\Seeder;
use Kwidoo\Mere\Models\MenuItem;
use Kwidoo\Mere\Data\MenuConfigurationData;
use Kwidoo\Mere\Data\MetaData;
use Kwidoo\Mere\Data\FieldDefinitionData;
use Kwidoo\Mere\Data\RoleConfigurationData;
use Kwidoo\Mere\Data\ValidationRulesData;

class OrganizationsMenuSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            new FieldDefinitionData(key: 'id', label: 'ID', type: 'uuid', editable: false, visible: true, sortable: true),
            new FieldDefinitionData(key: 'name', label: 'Name', type: 'text', editable: true, visible: true, sortable: true),
            new FieldDefinitionData(key: 'slug', label: 'Slug', type: 'text', editable: true, visible: true, sortable: true),
            new FieldDefinitionData(key: 'owner_id', label: 'Owner', type: 'uuid', editable: true, visible: true),
            new FieldDefinitionData(key: 'description', label: 'Description', type: 'textarea', editable: true, visible: true),
            new FieldDefinitionData(key: 'logo', label: 'Logo', type: 'text', editable: true, visible: true),
            new FieldDefinitionData(key: 'created_at', label: 'Created At', type: 'datetime', editable: false, visible: true, sortable: true),
            new FieldDefinitionData(key: 'updated_at', label: 'Updated At', type: 'datetime', editable: false, visible: false),
        ];

        $rules = new ValidationRulesData([
            'create' => [
                'name' => 'required|string|max:255',
                'slug' => 'required|string|unique:organizations,slug',
                'owner_id' => 'nullable|uuid|exists:users,id',
                'description' => 'nullable|string',
                'logo' => 'nullable|string',
            ],
            'update' => [
                'name' => 'sometimes|string|max:255',
                'slug' => 'sometimes|string|unique:organizations,slug,{{id}}',
                'owner_id' => 'nullable|uuid|exists:users,id',
                'description' => 'nullable|string',
                'logo' => 'nullable|string',
            ],
        ]);

        $config = new MenuConfigurationData(
            version: 'v1',
            meta: new MetaData(
                resource: 'Organization',
                translationKey: 'organizations',
                apiEndpoint: '/api/organizations'
            ),
            default: new RoleConfigurationData(
                fields: $fields,
                actions: ['create' => true, 'update' => true, 'delete' => true],
                rules: $rules
            ),
            roles: null
        );

        MenuItem::updateOrCreate(
            ['name' => 'organizations'],
            [
                'path' => 'organizations',
                'component' => 'de',
                'props' => $config
            ]
        );
    }
}
