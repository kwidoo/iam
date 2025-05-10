<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Kwidoo\Mere\Data\FieldDefinitionData;
use Kwidoo\Mere\Data\MenuConfigurationData;
use Kwidoo\Mere\Data\MetaData;
use Kwidoo\Mere\Data\RoleConfigurationData;
use Kwidoo\Mere\Data\ValidationRulesData;
use Kwidoo\Mere\Models\MenuItem;

class ResourceConfigSeeder extends Seeder
{
    /**
     * Seed the resource configurations.
     */
    public function run(): void
    {
        // Create the main menu groups
        $this->createUserManagementGroup();
        $this->createSystemManagementGroup();

        // User Management resources
        $this->createUserResourceConfig();
        $this->createRoleResourceConfig();
        $this->createPermissionResourceConfig();

        // System Management resources
        $this->createOrganizationResourceConfig();
        $this->createContactsResourceConfig();
        $this->createMicroservicesResourceConfig();
    }

    /**
     * Create the User Management menu group
     */
    private function createUserManagementGroup(): void
    {
        // Create or update the User Management group menu item
        MenuItem::updateOrCreate(
            ['path' => '/user-management'],
            [
                'name' => 'UserManagement',
                'component' => 'RouterView', // RouterView will render child routes
                'props' => new MenuConfigurationData(
                    version: '1.0',
                    meta: new MetaData(
                        resource: 'UserManagement',
                        translationKey: 'menu.userManagement',
                        apiEndpoint: 'user-management'
                    ),
                    default: new RoleConfigurationData(
                        fields: [],
                        actions: [
                            'create' => true,
                            'update' => true,
                            'delete' => true
                        ],
                        rules: new ValidationRulesData([])
                    ),
                    roles: []
                )
            ]
        );
    }

    /**
     * Create the System Management menu group
     */
    private function createSystemManagementGroup(): void
    {
        // Create or update the System Management group menu item
        MenuItem::updateOrCreate(
            ['path' => '/system-management'],
            [
                'name' => 'SystemManagement',
                'component' => 'RouterView', // RouterView will render child routes
                'props' => new MenuConfigurationData(
                    version: '1.0',
                    meta: new MetaData(
                        resource: 'SystemManagement',
                        translationKey: 'menu.systemManagement',
                        apiEndpoint: 'system-management'
                    ),
                    default: new RoleConfigurationData(
                        fields: [],
                        actions: [
                            'create' => true,
                            'update' => true,
                            'delete' => true
                        ],
                        rules: new ValidationRulesData([])
                    ),
                    roles: []
                )
            ]
        );
    }

    /**
     * Create the User resource configuration
     */
    private function createUserResourceConfig(): void
    {
        // Get the parent menu item (User Management group)
        $parentItem = MenuItem::where('path', '/user-management')->first();
        $parentId = $parentItem ? $parentItem->id : null;

        // Create User list view
        $userListItem = MenuItem::where('path', '/user-management/users')->first();

        if (!$userListItem) {
            $userListItem = new MenuItem([
                'path' => '/user-management/users',
                'name' => 'Users',
                'parent_id' => $parentId,
            ]);
        }

        $userListItem->component = 'GenericResource';
        $userListItem->parent_id = $parentId;
        $userListItem->props = new MenuConfigurationData(
            version: '1.0',
            meta: new MetaData(
                resource: 'User',
                translationKey: 'users.listTitle',
                apiEndpoint: 'users'
            ),
            default: new RoleConfigurationData(
                fields: [
                    new FieldDefinitionData(
                        key: 'id',
                        label: 'ID',
                        type: 'number',
                        editable: false,
                        visible: false,
                        sortable: true
                    ),
                    new FieldDefinitionData(
                        key: 'name',
                        label: 'Name',
                        type: 'text',
                        editable: true,
                        visible: true,
                        sortable: true
                    ),
                    new FieldDefinitionData(
                        key: 'email',
                        label: 'Email',
                        type: 'email',
                        editable: true,
                        visible: true,
                        sortable: true
                    ),
                    new FieldDefinitionData(
                        key: 'created_at',
                        label: 'Created At',
                        type: 'datetime',
                        editable: false,
                        visible: true,
                        sortable: true
                    ),
                ],
                actions: [
                    'create' => true,
                    'update' => true,
                    'delete' => true
                ],
                rules: new ValidationRulesData([])
            ),
            roles: [
                'admin' => new RoleConfigurationData(
                    fields: [],
                    actions: [
                        'create' => true,
                        'update' => true,
                        'delete' => true
                    ],
                    rules: new ValidationRulesData([])
                ),
                'manager' => new RoleConfigurationData(
                    fields: [],
                    actions: [
                        'view' => true
                    ],
                    rules: new ValidationRulesData([])
                ),
            ]
        );
        $userListItem->save();

        // Create User create form
        MenuItem::create([
            'path' => '/user-management/users/create',
            'name' => 'UserCreate',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'User',
                    translationKey: 'users.createTitle',
                    apiEndpoint: 'users'
                ),
                default: new RoleConfigurationData(
                    fields: [
                        new FieldDefinitionData(
                            key: 'name',
                            label: 'Name',
                            type: 'text',
                            editable: true,
                            visible: true,
                            placeholder: 'Enter full name'
                        ),
                        new FieldDefinitionData(
                            key: 'email',
                            label: 'Email',
                            type: 'email',
                            editable: true,
                            visible: true,
                            placeholder: 'Enter email address'
                        ),
                        new FieldDefinitionData(
                            key: 'password',
                            label: 'Password',
                            type: 'password',
                            editable: true,
                            visible: true,
                            placeholder: 'Enter password'
                        ),
                        new FieldDefinitionData(
                            key: 'password_confirmation',
                            label: 'Confirm Password',
                            type: 'password',
                            editable: true,
                            visible: true,
                            placeholder: 'Confirm password'
                        ),
                    ],
                    actions: [
                        'create' => true
                    ],
                    rules: new ValidationRulesData([
                        'create' => [
                            'name' => 'required|string|max:255',
                            'email' => 'required|email|unique:users,email',
                            'password' => 'required|min:8|confirmed',
                        ]
                    ])
                ),
                roles: []
            )
        ])->appendToNode($userListItem)->save();

        // Create User edit form
        MenuItem::create([
            'path' => '/user-management/users/:id/edit',
            'name' => 'UserEdit',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'User',
                    translationKey: 'users.editTitle',
                    apiEndpoint: 'users'
                ),
                default: new RoleConfigurationData(
                    fields: [
                        new FieldDefinitionData(
                            key: 'name',
                            label: 'Name',
                            type: 'text',
                            editable: true,
                            visible: true
                        ),
                        new FieldDefinitionData(
                            key: 'email',
                            label: 'Email',
                            type: 'email',
                            editable: true,
                            visible: true
                        ),
                        new FieldDefinitionData(
                            key: 'password',
                            label: 'New Password',
                            type: 'password',
                            editable: true,
                            visible: true,
                            placeholder: 'Leave blank to keep current password'
                        ),
                        new FieldDefinitionData(
                            key: 'password_confirmation',
                            label: 'Confirm New Password',
                            type: 'password',
                            editable: true,
                            visible: true
                        ),
                    ],
                    actions: [
                        'update' => true
                    ],
                    rules: new ValidationRulesData([
                        'update' => [
                            'name' => 'required|string|max:255',
                            'email' => 'required|email|unique:users,email,{id}',
                            'password' => 'nullable|min:8|confirmed',
                        ]
                    ])
                ),
                roles: []
            )
        ])->appendToNode($userListItem)->save();

        // Create User view
        MenuItem::create([
            'path' => '/user-management/users/:id',
            'name' => 'UserShow',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'User',
                    translationKey: 'users.showTitle',
                    apiEndpoint: 'users'
                ),
                default: new RoleConfigurationData(
                    fields: [
                        new FieldDefinitionData(
                            key: 'id',
                            label: 'ID',
                            type: 'number',
                            editable: false,
                            visible: true
                        ),
                        new FieldDefinitionData(
                            key: 'name',
                            label: 'Name',
                            type: 'text',
                            editable: false,
                            visible: true
                        ),
                        new FieldDefinitionData(
                            key: 'email',
                            label: 'Email',
                            type: 'email',
                            editable: false,
                            visible: true
                        ),
                        new FieldDefinitionData(
                            key: 'created_at',
                            label: 'Created At',
                            type: 'datetime',
                            editable: false,
                            visible: true
                        ),
                        new FieldDefinitionData(
                            key: 'updated_at',
                            label: 'Updated At',
                            type: 'datetime',
                            editable: false,
                            visible: true
                        ),
                    ],
                    actions: [
                        'view' => true
                    ],
                    rules: new ValidationRulesData([])
                ),
                roles: []
            )
        ])->appendToNode($userListItem)->save();
    }

    /**
     * Create the Role resource configuration
     */
    private function createRoleResourceConfig(): void
    {
        // Get the parent menu item (User Management group)
        $parentItem = MenuItem::where('path', '/user-management')->first();
        $parentId = $parentItem ? $parentItem->id : null;

        // Create Role list view
        $roleListItem = MenuItem::where('path', '/user-management/roles')->first();

        if (!$roleListItem) {
            $roleListItem = new MenuItem([
                'path' => '/user-management/roles',
                'name' => 'Roles',
                'parent_id' => $parentId,
            ]);
        }

        $roleListItem->component = 'GenericResource';
        $roleListItem->parent_id = $parentId;
        $roleListItem->props = new MenuConfigurationData(
            version: '1.0',
            meta: new MetaData(
                resource: 'Role',
                translationKey: 'roles.listTitle',
                apiEndpoint: 'roles'
            ),
            default: new RoleConfigurationData(
                fields: [
                    new FieldDefinitionData(
                        key: 'id',
                        label: 'ID',
                        type: 'number',
                        editable: false,
                        visible: true,
                        sortable: true
                    ),
                    new FieldDefinitionData(
                        key: 'name',
                        label: 'Role Name',
                        type: 'text',
                        editable: true,
                        visible: true,
                        sortable: true
                    ),
                    new FieldDefinitionData(
                        key: 'created_at',
                        label: 'Created At',
                        type: 'datetime',
                        editable: false,
                        visible: true,
                        sortable: true
                    ),
                ],
                actions: [
                    'create' => true,
                    'update' => true,
                    'delete' => true
                ],
                rules: new ValidationRulesData([])
            ),
            roles: []
        );
        $roleListItem->save();

        // Create Role create form
        MenuItem::create([
            'path' => '/user-management/roles/create',
            'name' => 'RoleCreate',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'Role',
                    translationKey: 'roles.createTitle',
                    apiEndpoint: 'roles'
                ),
                default: new RoleConfigurationData(
                    fields: [
                        new FieldDefinitionData(
                            key: 'name',
                            label: 'Role Name',
                            type: 'text',
                            editable: true,
                            visible: true,
                            placeholder: 'Enter role name'
                        ),
                        new FieldDefinitionData(
                            key: 'permissions',
                            label: 'Permissions',
                            type: 'multiselect',
                            editable: true,
                            visible: true,
                            relation: [
                                'type' => 'belongsToMany',
                                'model' => 'Permission',
                                'valueKey' => 'id',
                                'labelKey' => 'name'
                            ]
                        ),
                    ],
                    actions: [
                        'create' => true
                    ],
                    rules: new ValidationRulesData([
                        'create' => [
                            'name' => 'required|string|max:255|unique:roles,name',
                            'permissions' => 'array',
                            'permissions.*' => 'exists:permissions,id',
                        ]
                    ])
                ),
                roles: []
            )
        ])->appendToNode($roleListItem)->save();

        // Create Role edit form
        MenuItem::create([
            'path' => '/user-management/roles/:id/edit',
            'name' => 'RoleEdit',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'Role',
                    translationKey: 'roles.editTitle',
                    apiEndpoint: 'roles'
                ),
                default: new RoleConfigurationData(
                    fields: [
                        new FieldDefinitionData(
                            key: 'name',
                            label: 'Role Name',
                            type: 'text',
                            editable: true,
                            visible: true
                        ),
                        new FieldDefinitionData(
                            key: 'permissions',
                            label: 'Permissions',
                            type: 'multiselect',
                            editable: true,
                            visible: true,
                            relation: [
                                'type' => 'belongsToMany',
                                'model' => 'Permission',
                                'valueKey' => 'id',
                                'labelKey' => 'name'
                            ]
                        ),
                    ],
                    actions: [
                        'update' => true
                    ],
                    rules: new ValidationRulesData([
                        'update' => [
                            'name' => 'required|string|max:255|unique:roles,name,{id}',
                            'permissions' => 'array',
                            'permissions.*' => 'exists:permissions,id',
                        ]
                    ])
                ),
                roles: []
            )
        ])->appendToNode($roleListItem)->save();

        // Create Role view
        MenuItem::create([
            'path' => '/user-management/roles/:id',
            'name' => 'RoleShow',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'Role',
                    translationKey: 'roles.showTitle',
                    apiEndpoint: 'roles'
                ),
                default: new RoleConfigurationData(
                    fields: [
                        new FieldDefinitionData(
                            key: 'id',
                            label: 'ID',
                            type: 'number',
                            editable: false,
                            visible: true
                        ),
                        new FieldDefinitionData(
                            key: 'name',
                            label: 'Role Name',
                            type: 'text',
                            editable: false,
                            visible: true
                        ),
                        new FieldDefinitionData(
                            key: 'permissions',
                            label: 'Permissions',
                            type: 'multiselect',
                            editable: false,
                            visible: true,
                            relation: [
                                'type' => 'belongsToMany',
                                'model' => 'Permission',
                                'valueKey' => 'id',
                                'labelKey' => 'name'
                            ]
                        ),
                        new FieldDefinitionData(
                            key: 'created_at',
                            label: 'Created At',
                            type: 'datetime',
                            editable: false,
                            visible: true
                        ),
                    ],
                    actions: [
                        'view' => true
                    ],
                    rules: new ValidationRulesData([])
                ),
                roles: []
            )
        ]);
    }

    /**
     * Create the Permission resource configuration
     */
    private function createPermissionResourceConfig(): void
    {
        // Get the parent menu item (User Management group)
        $parentItem = MenuItem::where('path', '/user-management')->first();
        $parentId = $parentItem ? $parentItem->id : null;

        // Create Permission list view
        $permissionListItem = MenuItem::where('path', '/user-management/permissions')->first();

        if (!$permissionListItem) {
            $permissionListItem = new MenuItem([
                'path' => '/user-management/permissions',
                'name' => 'Permissions',
                'parent_id' => $parentId,
            ]);
        }

        $permissionListItem->component = 'GenericResource';
        $permissionListItem->parent_id = $parentId;
        $permissionListItem->props = new MenuConfigurationData(
            version: '1.0',
            meta: new MetaData(
                resource: 'Permission',
                translationKey: 'permissions.listTitle',
                apiEndpoint: 'permissions'
            ),
            default: new RoleConfigurationData(
                fields: [
                    new FieldDefinitionData(
                        key: 'id',
                        label: 'ID',
                        type: 'number',
                        editable: false,
                        visible: true,
                        sortable: true
                    ),
                    new FieldDefinitionData(
                        key: 'name',
                        label: 'Permission Name',
                        type: 'text',
                        editable: true,
                        visible: true,
                        sortable: true
                    ),
                    new FieldDefinitionData(
                        key: 'created_at',
                        label: 'Created At',
                        type: 'datetime',
                        editable: false,
                        visible: true,
                        sortable: true
                    ),
                ],
                actions: [
                    'create' => true,
                    'update' => true,
                    'delete' => true
                ],
                rules: new ValidationRulesData([])
            ),
            roles: []
        );
        $permissionListItem->save();

        // Create Permission create form
        MenuItem::updateOrCreate(
            ['path' => '/user-management/permissions/create'],
            [
                'name' => 'PermissionCreate',
                'component' => 'GenericForm',
                'parent_id' => $parentId,
                'props' => new MenuConfigurationData(
                    version: '1.0',
                    meta: new MetaData(
                        resource: 'Permission',
                        translationKey: 'permissions.createTitle',
                        apiEndpoint: 'permissions'
                    ),
                    default: new RoleConfigurationData(
                        fields: [
                            new FieldDefinitionData(
                                key: 'name',
                                label: 'Permission Name',
                                type: 'text',
                                editable: true,
                                visible: true,
                                placeholder: 'Enter permission name'
                            ),
                        ],
                        actions: [
                            'create' => true
                        ],
                        rules: new ValidationRulesData([
                            'create' => [
                                'name' => 'required|string|max:255|unique:permissions,name',
                            ]
                        ])
                    ),
                    roles: []
                )
            ]
        )->appendToNode($permissionListItem)->save();

        // Create Permission edit form
        MenuItem::updateOrCreate(
            ['path' => '/user-management/permissions/:id/edit'],
            [
                'name' => 'PermissionEdit',
                'component' => 'GenericForm',
                'parent_id' => $parentId,
                'props' => new MenuConfigurationData(
                    version: '1.0',
                    meta: new MetaData(
                        resource: 'Permission',
                        translationKey: 'permissions.editTitle',
                        apiEndpoint: 'permissions'
                    ),
                    default: new RoleConfigurationData(
                        fields: [
                            new FieldDefinitionData(
                                key: 'name',
                                label: 'Permission Name',
                                type: 'text',
                                editable: true,
                                visible: true
                            ),
                        ],
                        actions: [
                            'update' => true
                        ],
                        rules: new ValidationRulesData([
                            'update' => [
                                'name' => 'required|string|max:255|unique:permissions,name,{id}',
                            ]
                        ])
                    ),
                    roles: []
                )
            ]
        )->appendToNode($permissionListItem)->save();
    }

    /**
     * Create the Organization resource configuration
     */
    private function createOrganizationResourceConfig(): void
    {
        $parentItem = MenuItem::where('path', '/user-management')->first();
        $parentId = $parentItem ? $parentItem->id : null;

        // Create Organization list view
        $orgListItem = MenuItem::where('path', '/user-management/organizations')->first();

        if (!$orgListItem) {
            $orgListItem = new MenuItem([
                'path' => '/user-management/organizations',
                'name' => 'Organizations',
                'parent_id' => $parentId,
            ]);
            $orgListItem->save();
        }


        if ($orgListItem) {
            $orgListItem->update([
                'component' => 'GenericResource',
                'props' => new MenuConfigurationData(
                    version: '1.0',
                    meta: new MetaData(
                        resource: 'Organization',
                        translationKey: 'organizations.listTitle',
                        apiEndpoint: 'organizations'
                    ),
                    default: new RoleConfigurationData(
                        fields: [
                            new FieldDefinitionData(
                                key: 'id',
                                label: 'ID',
                                type: 'number',
                                editable: false,
                                visible: true,
                                sortable: true
                            ),
                            new FieldDefinitionData(
                                key: 'name',
                                label: 'Organization Name',
                                type: 'text',
                                editable: true,
                                visible: true,
                                sortable: true
                            ),
                            new FieldDefinitionData(
                                key: 'created_at',
                                label: 'Created At',
                                type: 'datetime',
                                editable: false,
                                visible: true,
                                sortable: true
                            ),
                        ],
                        actions: [
                            'create' => true,
                            'update' => true,
                            'delete' => true
                        ],
                        rules: new ValidationRulesData([])
                    ),
                    roles: [
                        'super-admin' => new RoleConfigurationData(
                            fields: [],
                            actions: [
                                'create' => true,
                                'update' => true,
                                'delete' => true
                            ],
                            rules: new ValidationRulesData([])
                        ),
                        'admin' => new RoleConfigurationData(
                            fields: [],
                            actions: [
                                'view' => true
                            ],
                            rules: new ValidationRulesData([])
                        ),
                    ]
                )
            ]);
        }
        // Create Organization create form
        MenuItem::create([
            'path' => '/user-management/organizations/create',
            'name' => 'OrganizationCreate',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'Organization',
                    translationKey: 'organizations.createTitle',
                    apiEndpoint: 'organizations'
                ),
                default: new RoleConfigurationData(
                    fields: [
                        new FieldDefinitionData(
                            key: 'name',
                            label: 'Organization Name',
                            type: 'text',
                            editable: true,
                            visible: true,
                            placeholder: 'Enter organization name'
                        ),
                        new FieldDefinitionData(
                            key: 'description',
                            label: 'Description',
                            type: 'textarea',
                            editable: true,
                            visible: true,
                            placeholder: 'Enter organization description'
                        ),
                    ],
                    actions: [
                        'create' => true
                    ],
                    rules: new ValidationRulesData([
                        'create' => [
                            'name' => 'required|string|max:255',
                            'description' => 'nullable|string',
                        ]
                    ])
                ),
                roles: []
            )
        ])->appendToNode($orgListItem)->save();

        // Create Organization edit form
        MenuItem::create([
            'path' => '/user-management/organizations/:id/edit',
            'name' => 'OrganizationEdit',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'Organization',
                    translationKey: 'organizations.editTitle',
                    apiEndpoint: 'organizations'
                ),
                default: new RoleConfigurationData(
                    fields: [
                        new FieldDefinitionData(
                            key: 'name',
                            label: 'Organization Name',
                            type: 'text',
                            editable: true,
                            visible: true
                        ),
                        new FieldDefinitionData(
                            key: 'description',
                            label: 'Description',
                            type: 'textarea',
                            editable: true,
                            visible: true
                        ),
                    ],
                    actions: [
                        'update' => true
                    ],
                    rules: new ValidationRulesData([
                        'update' => [
                            'name' => 'required|string|max:255',
                            'description' => 'nullable|string',
                        ]
                    ])
                ),
                roles: []
            )
        ])->appendToNode($orgListItem)->save();

        // Create Organization view
        MenuItem::create([
            'path' => '/user-management/organizations/:id',
            'name' => 'OrganizationShow',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'Organization',
                    translationKey: 'organizations.showTitle',
                    apiEndpoint: 'organizations'
                ),
                default: new RoleConfigurationData(
                    fields: [
                        new FieldDefinitionData(
                            key: 'id',
                            label: 'ID',
                            type: 'number',
                            editable: false,
                            visible: true
                        ),
                        new FieldDefinitionData(
                            key: 'name',
                            label: 'Organization Name',
                            type: 'text',
                            editable: false,
                            visible: true
                        ),
                        new FieldDefinitionData(
                            key: 'description',
                            label: 'Description',
                            type: 'textarea',
                            editable: false,
                            visible: true
                        ),
                        new FieldDefinitionData(
                            key: 'created_at',
                            label: 'Created At',
                            type: 'datetime',
                            editable: false,
                            visible: true
                        ),
                    ],
                    actions: [
                        'view' => true
                    ],
                    rules: new ValidationRulesData([])
                ),
                roles: []
            )
        ])->appendToNode($orgListItem)->save();
    }

    /**
     * Create the Contacts resource configuration
     */
    private function createContactsResourceConfig(): void
    {
        // Get the parent menu item (System Management group)
        $parentItem = MenuItem::where('path', '/user-management')->first();
        $parentId = $parentItem ? $parentItem->id : null;

        // Get configuration from config files
        $verifierTypes = array_keys(config('contacts.verifiers', []));
        $uuidEnabled = config('contacts.uuid', false);
        $useUuidMorph = config('contacts.uuidMorph', false);

        // Create Contacts list view
        $contactsListItem = MenuItem::where('path', '/user-management/contacts')->first();

        if (!$contactsListItem) {
            $contactsListItem = new MenuItem([
                'path' => '/user-management/contacts',
                'name' => 'Contacts',
                'parent_id' => $parentId,
            ]);
        }

        $fields = [];

        if ($uuidEnabled) {
            $fields[] = new FieldDefinitionData(
                key: 'uuid',
                label: 'UUID',
                type: 'uuid',
                editable: false,
                visible: true,
                sortable: true
            );
        }

        $fields[] = new FieldDefinitionData(
            key: 'contactable_type',
            label: 'Contactable Type',
            type: 'text',
            editable: false,
            visible: true,
            sortable: true
        );

        $fields[] = new FieldDefinitionData(
            key: 'contactable_id',
            label: 'Contactable ID',
            type: $useUuidMorph ? 'uuid' : 'integer',
            editable: false,
            visible: true,
            sortable: true
        );

        $fields[] = new FieldDefinitionData(
            key: 'type',
            label: 'Type',
            type: 'select',
            editable: true,
            visible: true,
            sortable: true,
            options: $verifierTypes
        );

        $fields[] = new FieldDefinitionData(
            key: 'value',
            label: 'Value',
            type: 'text',
            editable: true,
            visible: true,
            sortable: true
        );

        $fields[] = new FieldDefinitionData(
            key: 'is_primary',
            label: 'Primary',
            type: 'boolean',
            editable: true,
            visible: true,
            sortable: true
        );

        $fields[] = new FieldDefinitionData(
            key: 'is_verified',
            label: 'Verified',
            type: 'boolean',
            editable: false,
            visible: true,
            sortable: true
        );

        $fields[] = new FieldDefinitionData(
            key: 'created_at',
            label: 'Created At',
            type: 'datetime',
            editable: false,
            visible: true,
            sortable: true
        );

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

        $contactsListItem->component = 'GenericResource';
        $contactsListItem->parent_id = $parentId;
        $contactsListItem->props = new MenuConfigurationData(
            version: '1.0',
            meta: new MetaData(
                resource: 'Contact',
                translationKey: 'contacts.listTitle',
                apiEndpoint: 'contacts'
            ),
            default: new RoleConfigurationData(
                fields: $fields,
                actions: [
                    'create' => true,
                    'update' => true,
                    'delete' => true
                ],
                rules: $rules
            ),
            roles: []
        );
        $contactsListItem->save();

        // Create Contact create form
        MenuItem::updateOrCreate(
            ['path' => '/user-management/contacts/create'],
            [
                'name' => 'ContactCreate',
                'component' => 'GenericForm',
                'parent_id' => $parentId,
                'props' => new MenuConfigurationData(
                    version: '1.0',
                    meta: new MetaData(
                        resource: 'Contact',
                        translationKey: 'contacts.createTitle',
                        apiEndpoint: 'contacts'
                    ),
                    default: new RoleConfigurationData(
                        fields: $fields,
                        actions: [
                            'create' => true
                        ],
                        rules: $rules
                    ),
                    roles: []
                )
            ]
        )->appendToNode($contactsListItem)->save();

        // Create Contact edit form
        MenuItem::updateOrCreate(
            ['path' => '/user-management/contacts/:id/edit'],
            [
                'name' => 'ContactEdit',
                'component' => 'GenericForm',
                'parent_id' => $parentId,
                'props' => new MenuConfigurationData(
                    version: '1.0',
                    meta: new MetaData(
                        resource: 'Contact',
                        translationKey: 'contacts.editTitle',
                        apiEndpoint: 'contacts'
                    ),
                    default: new RoleConfigurationData(
                        fields: $fields,
                        actions: [
                            'update' => true
                        ],
                        rules: $rules
                    ),
                    roles: []
                )
            ]
        )->appendToNode($contactsListItem)->save();
    }

    /**
     * Create the Microservices resource configuration
     */
    private function createMicroservicesResourceConfig(): void
    {
        // Get the parent menu item (System Management group)
        $parentItem = MenuItem::where('path', '/system-management')->first();
        $parentId = $parentItem ? $parentItem->id : null;

        // Create Microservices list view
        $microservicesListItem = MenuItem::where('path', '/system-management/microservices')->first();

        if (!$microservicesListItem) {
            $microservicesListItem = new MenuItem([
                'path' => '/system-management/microservices',
                'name' => 'Microservices',
                'parent_id' => $parentId,
            ]);
        }

        $microservicesListItem->component = 'GenericResource';
        $microservicesListItem->parent_id = $parentId;
        $microservicesListItem->props = new MenuConfigurationData(
            version: '1.0',
            meta: new MetaData(
                resource: 'Microservice',
                translationKey: 'microservices.listTitle',
                apiEndpoint: 'microservices'
            ),
            default: new RoleConfigurationData(
                fields: [
                    new FieldDefinitionData(
                        key: 'id',
                        label: 'ID',
                        type: 'number',
                        editable: false,
                        visible: true,
                        sortable: true
                    ),
                    new FieldDefinitionData(
                        key: 'name',
                        label: 'Name',
                        type: 'text',
                        editable: true,
                        visible: true,
                        sortable: true
                    ),
                    new FieldDefinitionData(
                        key: 'endpoint',
                        label: 'Endpoint',
                        type: 'text',
                        editable: true,
                        visible: true,
                        sortable: true
                    ),
                    new FieldDefinitionData(
                        key: 'api_key',
                        label: 'API Key',
                        type: 'text',
                        editable: true,
                        visible: false,
                        sortable: false
                    ),
                    new FieldDefinitionData(
                        key: 'status',
                        label: 'Status',
                        type: 'select',
                        editable: true,
                        visible: true,
                        sortable: true,
                        options: ['active', 'inactive', 'maintenance']
                    ),
                    new FieldDefinitionData(
                        key: 'created_at',
                        label: 'Created At',
                        type: 'datetime',
                        editable: false,
                        visible: true,
                        sortable: true
                    ),
                ],
                actions: [
                    'create' => true,
                    'update' => true,
                    'delete' => true
                ],
                rules: new ValidationRulesData([
                    'create' => [
                        'name' => 'required|string|max:255',
                        'endpoint' => 'required|url',
                        'api_key' => 'nullable|string',
                        'status' => 'required|in:active,inactive,maintenance',
                    ],
                    'update' => [
                        'name' => 'sometimes|string|max:255',
                        'endpoint' => 'sometimes|url',
                        'api_key' => 'nullable|string',
                        'status' => 'sometimes|in:active,inactive,maintenance',
                    ],
                ])
            ),
            roles: []
        );
        $microservicesListItem->save();

        // Create Microservice create form
        MenuItem::updateOrCreate(
            ['path' => '/system-management/microservices/create'],
            [
                'name' => 'MicroserviceCreate',
                'component' => 'GenericForm',
                'parent_id' => $parentId,
                'props' => new MenuConfigurationData(
                    version: '1.0',
                    meta: new MetaData(
                        resource: 'Microservice',
                        translationKey: 'microservices.createTitle',
                        apiEndpoint: 'microservices'
                    ),
                    default: new RoleConfigurationData(
                        fields: [
                            new FieldDefinitionData(
                                key: 'name',
                                label: 'Name',
                                type: 'text',
                                editable: true,
                                visible: true,
                                placeholder: 'Enter microservice name'
                            ),
                            new FieldDefinitionData(
                                key: 'endpoint',
                                label: 'Endpoint',
                                type: 'text',
                                editable: true,
                                visible: true,
                                placeholder: 'https://api.example.com'
                            ),
                            new FieldDefinitionData(
                                key: 'api_key',
                                label: 'API Key',
                                type: 'text',
                                editable: true,
                                visible: true,
                                placeholder: 'Enter API key if required'
                            ),
                            new FieldDefinitionData(
                                key: 'status',
                                label: 'Status',
                                type: 'select',
                                editable: true,
                                visible: true,
                                options: ['active', 'inactive', 'maintenance']
                            ),
                        ],
                        actions: [
                            'create' => true
                        ],
                        rules: new ValidationRulesData([
                            'create' => [
                                'name' => 'required|string|max:255',
                                'endpoint' => 'required|url',
                                'api_key' => 'nullable|string',
                                'status' => 'required|in:active,inactive,maintenance',
                            ],
                        ])
                    ),
                    roles: []
                )
            ]
        )->appendToNode($microservicesListItem)->save();

        // Create Microservice edit form
        MenuItem::updateOrCreate(
            ['path' => '/system-management/microservices/:id/edit'],
            [
                'name' => 'MicroserviceEdit',
                'component' => 'GenericForm',
                'parent_id' => $parentId,
                'props' => new MenuConfigurationData(
                    version: '1.0',
                    meta: new MetaData(
                        resource: 'Microservice',
                        translationKey: 'microservices.editTitle',
                        apiEndpoint: 'microservices'
                    ),
                    default: new RoleConfigurationData(
                        fields: [
                            new FieldDefinitionData(
                                key: 'name',
                                label: 'Name',
                                type: 'text',
                                editable: true,
                                visible: true
                            ),
                            new FieldDefinitionData(
                                key: 'endpoint',
                                label: 'Endpoint',
                                type: 'text',
                                editable: true,
                                visible: true
                            ),
                            new FieldDefinitionData(
                                key: 'api_key',
                                label: 'API Key',
                                type: 'text',
                                editable: true,
                                visible: true
                            ),
                            new FieldDefinitionData(
                                key: 'status',
                                label: 'Status',
                                type: 'select',
                                editable: true,
                                visible: true,
                                options: ['active', 'inactive', 'maintenance']
                            ),
                        ],
                        actions: [
                            'update' => true
                        ],
                        rules: new ValidationRulesData([
                            'update' => [
                                'name' => 'sometimes|string|max:255',
                                'endpoint' => 'sometimes|url',
                                'api_key' => 'nullable|string',
                                'status' => 'sometimes|in:active,inactive,maintenance',
                            ],
                        ])
                    ),
                    roles: []
                )
            ]
        )->appendToNode($microservicesListItem)->save();

        // Create Microservice view
        MenuItem::updateOrCreate(
            ['path' => '/system-management/microservices/:id'],
            [
                'name' => 'MicroserviceShow',
                'component' => 'GenericForm',
                'parent_id' => $parentId,
                'props' => new MenuConfigurationData(
                    version: '1.0',
                    meta: new MetaData(
                        resource: 'Microservice',
                        translationKey: 'microservices.showTitle',
                        apiEndpoint: 'microservices'
                    ),
                    default: new RoleConfigurationData(
                        fields: [
                            new FieldDefinitionData(
                                key: 'id',
                                label: 'ID',
                                type: 'number',
                                editable: false,
                                visible: true
                            ),
                            new FieldDefinitionData(
                                key: 'name',
                                label: 'Name',
                                type: 'text',
                                editable: false,
                                visible: true
                            ),
                            new FieldDefinitionData(
                                key: 'endpoint',
                                label: 'Endpoint',
                                type: 'text',
                                editable: false,
                                visible: true
                            ),
                            new FieldDefinitionData(
                                key: 'status',
                                label: 'Status',
                                type: 'select',
                                editable: false,
                                visible: true,
                                options: ['active', 'inactive', 'maintenance']
                            ),
                            new FieldDefinitionData(
                                key: 'created_at',
                                label: 'Created At',
                                type: 'datetime',
                                editable: false,
                                visible: true
                            ),
                        ],
                        actions: [
                            'view' => true
                        ],
                        rules: new ValidationRulesData([])
                    ),
                    roles: []
                )
            ]
        )->appendToNode($microservicesListItem)->save();
    }
}
