<?php

namespace Database\Seeders\Menus;

use Illuminate\Database\Seeder;
use Kwidoo\Mere\Models\MenuItem;
use Kwidoo\Mere\Data\MenuConfigurationData;
use Kwidoo\Mere\Data\MetaData;
use Kwidoo\Mere\Data\FieldDefinitionData;
use Kwidoo\Mere\Data\RoleConfigurationData;
use Kwidoo\Mere\Data\ValidationRulesData;

class ProfilesMenuSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            new FieldDefinitionData(key: 'id', label: 'ID', type: 'uuid', editable: false, visible: true),
            new FieldDefinitionData(key: 'fname', label: 'First Name', type: 'text', editable: true, visible: true),
            new FieldDefinitionData(key: 'lname', label: 'Last Name', type: 'text', editable: true, visible: true),
            new FieldDefinitionData(key: 'dob', label: 'Date of Birth', type: 'date', editable: true, visible: true),
            new FieldDefinitionData(key: 'gender', label: 'Gender', type: 'select', editable: true, visible: true, options: ['m', 'f']),
            new FieldDefinitionData(key: 'user_id', label: 'User ID', type: 'uuid', editable: true, visible: false),
            new FieldDefinitionData(key: 'full_name', label: 'Full Name', type: 'text', editable: false, visible: true),
            new FieldDefinitionData(key: 'created_at', label: 'Created At', type: 'datetime', editable: false, visible: true),
            new FieldDefinitionData(key: 'updated_at', label: 'Updated At', type: 'datetime', editable: false, visible: false),
            new FieldDefinitionData(key: 'deleted_at', label: 'Deleted At', type: 'datetime', editable: false, visible: false),
        ];

        $rules = new ValidationRulesData([
            'create' => [
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'dob' => 'nullable|date',
                'gender' => 'nullable|in:m,f',
                'user_id' => 'required|uuid|exists:users,id',
            ],
            'update' => [
                'fname' => 'sometimes|string|max:255',
                'lname' => 'sometimes|string|max:255',
                'dob' => 'nullable|date',
                'gender' => 'nullable|in:m,f',
            ],
        ]);

        $config = new MenuConfigurationData(
            version: 'v1',
            meta: new MetaData(
                resource: 'Profile',
                translationKey: 'profiles',
                apiEndpoint: '/api/profiles'
            ),
            default: new RoleConfigurationData(
                fields: $fields,
                actions: ['create' => true, 'update' => true, 'delete' => true],
                rules: $rules
            ),
            roles: null
        );

        MenuItem::updateOrCreate(
            ['name' => 'profiles'],
            [
                'path' => 'profiles',
                'component' => 'de',
                'props' => $config
            ]
        );
    }
}
