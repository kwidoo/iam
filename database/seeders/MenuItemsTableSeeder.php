<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Kwidoo\Mere\Contracts\MenuRepository;

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
     * @param  MenuRepository  $menuRepository
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
        return;
        // Clear existing menu items
        $this->menuRepository->deleteWhere([]);

        // Create Organization menu items
        $this->menuRepository->create([
            'path' => '/organizations',
            'name' => 'OrganizationList',
            'component' => 'GenericResource',
            'props' => [
                'label' => 'organizations',
                'api' => 'organizations',
                'fields' => [
                    ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'visible' => true],
                    ['key' => 'description', 'label' => 'Description', 'sortable' => true, 'visible' => true],
                    ['key' => 'logo', 'label' => 'Logo', 'sortable' => true, 'visible' => true],
                ],
                'actions' => [
                    'create' => true,
                    'edit' => true,
                    'delete' => true
                ]
            ],
            '_lft' => 1,
            '_rgt' => 2
        ]);

        $this->menuRepository->create([
            'path' => '/organizations/create',
            'name' => 'OrganizationCreate',
            'component' => 'GenericForm',
            'props' => [
                'label' => 'organizations',
                'api' => 'organizations',
                'fields' => [
                    ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'visible' => true],
                    ['key' => 'owner_id', 'label' => 'Owner Id', 'sortable' => true, 'visible' => false],
                    ['key' => 'description', 'label' => 'Description', 'sortable' => true, 'visible' => true],
                    ['key' => 'logo', 'label' => 'Logo', 'sortable' => true, 'visible' => true],
                ],
                'rules' => [
                    'name' => ['required', 'string', 'max:255'],
                    'description' => ['nullable', 'string'],
                    'logo' => ['nullable', 'string']
                ]
            ],
            '_lft' => 3,
            '_rgt' => 4
        ]);

        $this->menuRepository->create([
            'path' => '/organizations/edit/:id',
            'name' => 'OrganizationUpdate',
            'component' => 'GenericForm',
            'props' => [
                'label' => 'organizations',
                'api' => 'organizations',
                'fields' => [
                    ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'visible' => true],
                    ['key' => 'slug', 'label' => 'Slug', 'sortable' => true, 'visible' => true, 'readonly' => true],
                    ['key' => 'owner_id', 'label' => 'Owner Id', 'sortable' => true, 'visible' => true],
                    ['key' => 'description', 'label' => 'Description', 'sortable' => true, 'visible' => true],
                    ['key' => 'logo', 'label' => 'Logo', 'sortable' => true, 'visible' => true],
                ],
                'rules' => [
                    'name' => ['sometimes', 'required', 'string', 'max:255'],
                    'description' => ['nullable', 'string'],
                    'logo' => ['nullable', 'string']
                ]
            ],
            '_lft' => 5,
            '_rgt' => 6
        ]);

        $this->menuRepository->create([
            'path' => '/profiles',
            'name' => 'ProfileList',
            'component' => 'GenericResource',
            'props' => [
                'label' => 'profiles',
                'api' => 'profiles',
                'fields' => [
                    ['key' => 'id', 'label' => 'Uuid', 'sortable' => true, 'visible' => true],
                ],
                'actions' => [
                    'create' => true,
                    'edit' => true,
                    'delete' => true
                ]
            ],
            '_lft' => 7,
            '_rgt' => 8
        ]);

        // Create User menu items
        $this->menuRepository->create([
            'path' => '/users',
            'name' => 'UserList',
            'component' => 'GenericResource',
            'props' => [
                'label' => 'users',
                'api' => 'admin/users',
                'fields' => [
                    ['key' => 'id', 'label' => 'Uuid', 'sortable' => true, 'visible' => true],
                ],
                'actions' => [
                    'create' => true,
                    'edit' => true,
                    'delete' => true
                ]
            ],
            '_lft' => 7,
            '_rgt' => 8
        ]);

        $this->menuRepository->create([
            'path' => '/users/create',
            'name' => 'RegistrationCreate',
            'component' => 'GenericForm',
            'props' => [
                'label' => 'users',
                'api' => 'admin/users',
                'fields' => [
                    ['key' => 'id', 'label' => 'Uuid', 'sortable' => true, 'visible' => true],
                    ['key' => 'password', 'label' => 'Password', 'sortable' => true, 'visible' => true],
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
        ]);

        $this->menuRepository->create([
            'path' => '/users/edit/:id',
            'name' => 'UserUpdate',
            'component' => 'GenericForm',
            'props' => [
                'label' => 'users',
                'api' => 'admin/users',
                'fields' => [
                    ['key' => 'id', 'label' => 'Uuid', 'sortable' => true, 'visible' => true],
                    ['key' => 'password', 'label' => 'Password', 'sortable' => true, 'visible' => true],
                ],
                'rules' => []
            ],
            '_lft' => 11,
            '_rgt' => 12
        ]);
    }
}
