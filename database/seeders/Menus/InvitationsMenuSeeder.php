<?php

namespace Database\Seeders\Menus;

use Illuminate\Database\Seeder;
use Kwidoo\Mere\Models\MenuItem;
use Kwidoo\Mere\Data\MenuConfigurationData;
use Kwidoo\Mere\Data\MetaData;
use Kwidoo\Mere\Data\FieldDefinitionData;
use Kwidoo\Mere\Data\RoleConfigurationData;
use Kwidoo\Mere\Data\ValidationRulesData;

class InvitationsMenuSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            new FieldDefinitionData(key: 'id', label: 'ID', type: 'uuid', editable: false, visible: true),
            new FieldDefinitionData(key: 'organization_id', label: 'Organization', type: 'uuid', editable: true, visible: true),
            new FieldDefinitionData(key: 'invited_by', label: 'Invited By', type: 'uuid', editable: true, visible: true),
            new FieldDefinitionData(key: 'contact_type', label: 'Contact Type', type: 'enum', editable: true, visible: true, options: ['email', 'phone', 'code']),
            new FieldDefinitionData(key: 'contact_value', label: 'Contact Value', type: 'text', editable: true, visible: true),
            new FieldDefinitionData(key: 'token', label: 'Token', type: 'text', editable: false, visible: true),
            new FieldDefinitionData(key: 'expires_at', label: 'Expires At', type: 'datetime', editable: true, visible: true),
            new FieldDefinitionData(key: 'accepted_at', label: 'Accepted At', type: 'datetime', editable: false, visible: true),
            new FieldDefinitionData(key: 'created_at', label: 'Created At', type: 'datetime', editable: false, visible: true),
            new FieldDefinitionData(key: 'updated_at', label: 'Updated At', type: 'datetime', editable: false, visible: false),
        ];

        $rules = new ValidationRulesData([
            'create' => [
                'organization_id' => 'required|uuid|exists:organizations,id',
                'invited_by' => 'required|uuid|exists:users,id',
                'contact_type' => 'required|in:email,phone,code',
                'contact_value' => 'required|string|max:255',
                'expires_at' => 'nullable|date',
            ],
            'update' => [
                'expires_at' => 'nullable|date',
            ],
        ]);

        $config = new MenuConfigurationData(
            version: 'v1',
            meta: new MetaData(
                resource: 'Invitation',
                translationKey: 'invitations',
                apiEndpoint: '/api/invitations'
            ),
            default: new RoleConfigurationData(
                fields: $fields,
                actions: ['create' => true, 'update' => true, 'delete' => true],
                rules: $rules
            ),
            roles: null
        );

        MenuItem::updateOrCreate(
            ['name' => 'invitations'],
            [
                'path' => 'invitations',
                'component' => 'de',
                'props' => $config
            ]
        );
    }
}
