<?php

namespace Database\Seeders\Menus;

use Illuminate\Database\Seeder;
use Kwidoo\Mere\Models\MenuItem;
use Kwidoo\Mere\Data\MenuConfigurationData;
use Kwidoo\Mere\Data\MetaData;
use Kwidoo\Mere\Data\FieldDefinitionData;
use Kwidoo\Mere\Data\RoleConfigurationData;
use Kwidoo\Mere\Data\ValidationRulesData;

class OrganizationProfileMenuSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            new FieldDefinitionData(key: 'organization_id', label: 'Organization', type: 'uuid', editable: true, visible: true),
            new FieldDefinitionData(key: 'profile_id', label: 'Profile', type: 'uuid', editable: true, visible: true),
        ];

        $rules = new ValidationRulesData([
            'create' => [
                'organization_id' => 'required|uuid|exists:organizations,id',
                'profile_id' => 'required|uuid|exists:profiles,id',
            ],
            'update' => [], // no editable fields expected on update
        ]);

        $config = new MenuConfigurationData(
            version: 'v1',
            meta: new MetaData(
                resource: 'OrganizationProfile',
                translationKey: 'organization_profile',
                apiEndpoint: '/api/organization-profiles'
            ),
            default: new RoleConfigurationData(
                fields: $fields,
                actions: ['create' => true, 'update' => false, 'delete' => true],
                rules: $rules
            ),
            roles: null
        );

        MenuItem::updateOrCreate(
            ['name' => 'organization_profile'],
            [
                'path' => 'organization_profile',
                'component' => 'de',
                'props' => $config
            ]
        );
    }
}
