<?php

namespace Database\Seeders\Menus;

use Illuminate\Database\Seeder;
use Kwidoo\Mere\Models\MenuItem;
use Kwidoo\Mere\Data\MenuConfigurationData;
use Kwidoo\Mere\Data\MetaData;
use Kwidoo\Mere\Data\FieldDefinitionData;
use Kwidoo\Mere\Data\RoleConfigurationData;
use Kwidoo\Mere\Data\ValidationRulesData;

class MicroservicesMenuSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            new FieldDefinitionData(key: 'uuid', label: 'UUID', type: 'uuid', editable: false, visible: true),
            new FieldDefinitionData(key: 'name', label: 'Name', type: 'text', editable: true, visible: true, sortable: true),
            new FieldDefinitionData(key: 'endpoint', label: 'Endpoint', type: 'text', editable: true, visible: true),
            new FieldDefinitionData(key: 'api_key', label: 'API Key', type: 'text', editable: true, visible: false),
            new FieldDefinitionData(key: 'status', label: 'Status', type: 'text', editable: true, visible: true),
            new FieldDefinitionData(key: 'created_at', label: 'Created At', type: 'datetime', editable: false, visible: true),
            new FieldDefinitionData(key: 'updated_at', label: 'Updated At', type: 'datetime', editable: false, visible: false),
        ];

        $rules = new ValidationRulesData([
            'create' => [
                'name' => 'required|string|unique:microservices,name',
                'endpoint' => 'required|url',
                'api_key' => 'required|string',
                'status' => 'required|string',
            ],
            'update' => [
                'name' => 'sometimes|string|unique:microservices,name,{{uuid}},uuid',
                'endpoint' => 'sometimes|url',
                'api_key' => 'sometimes|string',
                'status' => 'sometimes|string',
            ],
        ]);

        $config = new MenuConfigurationData(
            version: 'v1',
            meta: new MetaData(
                resource: 'Microservice',
                translationKey: 'microservices',
                apiEndpoint: '/api/microservices'
            ),
            default: new RoleConfigurationData(
                fields: $fields,
                actions: ['create' => true, 'update' => true, 'delete' => true],
                rules: $rules
            ),
            roles: null
        );

        MenuItem::updateOrCreate(
            ['name' => 'microservices'],
            [
                'path' => 'microservices',
                'component' => 'de',
                'props' => $config
            ]
        );
    }
}
