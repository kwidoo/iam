<?php

namespace Database\Seeders\Menus;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Kwidoo\Mere\Models\MenuItem;
use Kwidoo\Mere\Data\MenuConfigurationData;
use Kwidoo\Mere\Data\MetaData;
use Kwidoo\Mere\Data\FieldDefinitionData;
use Kwidoo\Mere\Data\RoleConfigurationData;
use Kwidoo\Mere\Data\ValidationRulesData;

class ContactsMenuSeeder extends Seeder
{
    public function run(): void
    {
        $verifierTypes = array_keys(Config::get('contacts.verifiers', []));
        $uuidEnabled = Config::get('contacts.uuid', false);
        $useUuidMorph = Config::get('contacts.uuidMorph', false);
        $table = Config::get('contacts.table', 'contacts');

        $fields = [];

        if ($uuidEnabled) {
            $fields[] = new FieldDefinitionData(key: 'uuid', label: 'UUID', type: 'uuid', editable: false, visible: true, sortable: true);
        }

        $fields[] = new FieldDefinitionData(key: 'contactable_type', label: 'Contactable Type', type: 'text', editable: false, visible: true);
        $fields[] = new FieldDefinitionData(key: 'contactable_id', label: 'Contactable ID', type: $useUuidMorph ? 'uuid' : 'integer', editable: false, visible: true);
        $fields[] = new FieldDefinitionData(key: 'type', label: 'Type', type: 'select', editable: true, visible: true, options: $verifierTypes);
        $fields[] = new FieldDefinitionData(key: 'value', label: 'Value', type: 'text', editable: true, visible: true);
        $fields[] = new FieldDefinitionData(key: 'is_primary', label: 'Primary', type: 'boolean', editable: true, visible: true);
        $fields[] = new FieldDefinitionData(key: 'is_verified', label: 'Verified', type: 'boolean', editable: false, visible: true);
        $fields[] = new FieldDefinitionData(key: 'created_at', label: 'Created At', type: 'datetime', editable: false, visible: true);
        $fields[] = new FieldDefinitionData(key: 'updated_at', label: 'Updated At', type: 'datetime', editable: false, visible: false);
        $fields[] = new FieldDefinitionData(key: 'deleted_at', label: 'Deleted At', type: 'datetime', editable: false, visible: false);

        $rules = new ValidationRulesData([
            'create' => [
                'contactable_type' => 'required|string',
                'contactable_id' => $useUuidMorph ? 'required|uuid' : 'required|integer',
                'type' => 'required|in:' . implode(',', $verifierTypes),
                'value' => 'required|string|max:255',
                'is_primary' => 'boolean',
            ],
            'update' => [
                'type' => 'sometimes|in:' . implode(',', $verifierTypes),
                'value' => 'sometimes|string|max:255',
                'is_primary' => 'sometimes|boolean',
            ],
        ]);

        $config = new MenuConfigurationData(
            version: 'v1',
            meta: new MetaData(
                resource: 'Contact',
                translationKey: 'contacts',
                apiEndpoint: "/api/{$table}"
            ),
            default: new RoleConfigurationData(
                fields: $fields,
                actions: ['create' => true, 'update' => true, 'delete' => true],
                rules: $rules
            ),
            roles: null
        );

        MenuItem::updateOrCreate(
            ['name' => $table],
            [
                'path' => 'contacts',
                'component' => 'Contacts',
                'props' => $config
            ]
        );
    }
}
