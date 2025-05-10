<?php

/**
 * This file contains the seeder for the menu items table.
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Kwidoo\Mere\Contracts\MenuRepository;

/**
 * Seeds the menu items table with default data.
 */
class MenuItemsTableSeeder extends Seeder
{
    /**
     * The menu repository instance.
     *
     * @var MenuRepository
     */
    protected $menuRepository;

    /**
     * Create a new seeder instance.
     *
     * @param  MenuRepository $menuRepository The menu repository instance.
     * @return void
     */
    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing menu items
        $this->menuRepository->deleteWhere([]);

        // Create Organization menu items
        $this->menuRepository->create(
            [
                'path' => '/organizations',
                'name' => 'OrganizationList',
                'component' => 'GenericResource',
                'props' => [
                    'label' => 'resource.organizations.label',
                    'api' => 'organizations',
                    'fields' => [
                        ['key' => 'name', 'label' => 'resource.organizations.fields.name', 'sortable' => true, 'visible' => true],
                        ['key' => 'description', 'label' => 'resource.organizations.fields.description', 'sortable' => true, 'visible' => true],
                        ['key' => 'logo', 'label' => 'resource.organizations.fields.logo', 'sortable' => true, 'visible' => true],
                    ],
                    'actions' => [
                        'create' => true,
                        'edit' => true,
                        'delete' => true
                    ]
                ],

            ]
        );

        $this->menuRepository->create(
            [
                'path' => '/organizations/create',
                'name' => 'OrganizationCreate',
                'component' => 'GenericForm',
                'props' => [
                    'label' => 'resource.organizations.create.label',
                    'api' => 'organizations',
                    'fields' => [
                        ['key' => 'name', 'label' => 'resource.organizations.fields.name', 'sortable' => true, 'visible' => true],
                        ['key' => 'owner_id', 'label' => 'resource.organizations.fields.owner_id', 'sortable' => true, 'visible' => false],
                        ['key' => 'description', 'label' => 'resource.organizations.fields.description', 'sortable' => true, 'visible' => true],
                        ['key' => 'logo', 'label' => 'resource.organizations.fields.logo', 'sortable' => true, 'visible' => true],
                    ],
                    'rules' => [
                        'name' => ['required', 'string', 'max:255'],
                        'description' => ['nullable', 'string'],
                        'logo' => ['nullable', 'string']
                    ]
                ],
                '_lft' => 3,
                '_rgt' => 4
            ]
        );

        $this->menuRepository->create(
            [
                'path' => '/organizations/edit/:id',
                'name' => 'OrganizationUpdate',
                'component' => 'GenericForm',
                'props' => [
                    'label' => 'resource.organizations.update.label',
                    'api' => 'organizations',
                    'fields' => [
                        ['key' => 'name', 'label' => 'resource.organizations.fields.name', 'sortable' => true, 'visible' => true],
                        ['key' => 'slug', 'label' => 'resource.organizations.fields.slug', 'sortable' => true, 'visible' => true, 'readonly' => true],
                        ['key' => 'owner_id', 'label' => 'resource.organizations.fields.owner_id', 'sortable' => true, 'visible' => true],
                        ['key' => 'description', 'label' => 'resource.organizations.fields.description', 'sortable' => true, 'visible' => true],
                        ['key' => 'logo', 'label' => 'resource.organizations.fields.logo', 'sortable' => true, 'visible' => true],
                    ],
                    'rules' => [
                        'name' => ['sometimes', 'required', 'string', 'max:255'],
                        'description' => ['nullable', 'string'],
                        'logo' => ['nullable', 'string']
                    ]
                ],
                '_lft' => 5,
                '_rgt' => 6
            ]
        );

        $this->menuRepository->create(
            [
                'path' => '/profiles',
                'name' => 'ProfileList',
                'component' => 'GenericResource',
                'props' => [
                    'label' => 'resource.profiles.label',
                    'api' => 'profiles',
                    'fields' => [
                        ['key' => 'id', 'label' => 'resource.profiles.fields.id', 'sortable' => true, 'visible' => true],
                    ],
                    'actions' => [
                        'create' => true,
                        'edit' => true,
                        'delete' => true
                    ]
                ],
                '_lft' => 7,
                '_rgt' => 8
            ]
        );

        // Create User menu items
        $this->menuRepository->create(
            [
                'path' => '/users',
                'name' => 'UserList',
                'component' => 'GenericResource',
                'props' => [
                    'label' => 'resource.users.label',
                    'api' => 'admin/users',
                    'fields' => [
                        ['key' => 'id', 'label' => 'resource.users.fields.id', 'sortable' => true, 'visible' => true],
                    ],
                    'actions' => [
                        'create' => true,
                        'edit' => true,
                        'delete' => true
                    ]
                ],
                '_lft' => 7,
                '_rgt' => 8
            ]
        );

        $this->menuRepository->create(
            [
                'path' => '/users/create',
                'name' => 'RegistrationCreate',
                'component' => 'GenericForm',
                'props' => [
                    'label' => 'resource.users.create.label',
                    'api' => 'admin/users',
                    'fields' => [
                        ['key' => 'id', 'label' => 'resource.users.fields.id', 'sortable' => true, 'visible' => true],
                        ['key' => 'password', 'label' => 'resource.users.fields.password', 'sortable' => true, 'visible' => true],
                    ],
                    'rules' => [
                        'create' => [
                            // 'method' => ['required', 'in:\'email\',\phone\'\''],
                            // 'otp' => ['required', 'boolean']
                        ]
                    ]
                ],
                '_lft' => 9,
                '_rgt' => 10
            ]
        );

        $this->menuRepository->create(
            [
                'path' => '/users/edit/:id',
                'name' => 'UserUpdate',
                'component' => 'GenericForm',
                'props' => [
                    'label' => 'resource.users.update.label',
                    'api' => 'admin/users',
                    'fields' => [
                        ['key' => 'id', 'label' => 'resource.users.fields.id', 'sortable' => true, 'visible' => true],
                        ['key' => 'password', 'label' => 'resource.users.fields.password', 'sortable' => true, 'visible' => true],
                    ],
                    'rules' => []
                ],
                '_lft' => 11,
                '_rgt' => 12
            ]
        );
    }
}
