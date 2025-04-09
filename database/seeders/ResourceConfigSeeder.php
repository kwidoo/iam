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
        $this->createUserResourceConfig();
        $this->createRoleResourceConfig();
        $this->createOrganizationResourceConfig();
        $this->createPermissionResourceConfig();
    }

    /**
     * Create the User resource configuration
     */
    private function createUserResourceConfig(): void
    {
        // Create User list view
        $userListItem = MenuItem::where('path', '/user-management/users')->first();

        if ($userListItem) {
            $userListItem->update([
                'component' => 'GenericResource',
                'props' => new MenuConfigurationData(
                    version: '1.0',
                    meta: new MetaData(
                        resource: 'User',
                        translationKey: 'user.list_title',
                        apiEndpoint: 'users'
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
                        actions: ['create', 'view', 'edit', 'delete'],
                        rules: new ValidationRulesData([])
                    ),
                    roles: [
                        'admin' => new RoleConfigurationData(
                            fields: [],
                            actions: ['create', 'view', 'edit', 'delete'],
                            rules: new ValidationRulesData([])
                        ),
                        'manager' => new RoleConfigurationData(
                            fields: [],
                            actions: ['view'],
                            rules: new ValidationRulesData([])
                        ),
                    ]
                )
            ]);
        }

        // Create User create form
        MenuItem::create([
            'path' => '/user-management/users/create',
            'name' => 'UserCreate',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'User',
                    translationKey: 'user.create_title',
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
                    actions: ['create'],
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
        ]);

        // Create User edit form
        MenuItem::create([
            'path' => '/user-management/users/:id/edit',
            'name' => 'UserEdit',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'User',
                    translationKey: 'user.edit_title',
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
                    actions: ['update'],
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
        ]);

        // Create User view
        MenuItem::create([
            'path' => '/user-management/users/:id',
            'name' => 'UserShow',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'User',
                    translationKey: 'user.show_title',
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
                    actions: ['view'],
                    rules: new ValidationRulesData([])
                ),
                roles: []
            )
        ]);
    }

    /**
     * Create the Role resource configuration
     */
    private function createRoleResourceConfig(): void
    {
        // Create Role list view
        $roleListItem = MenuItem::where('path', '/user-management/roles')->first();

        if ($roleListItem) {
            $roleListItem->update([
                'component' => 'GenericResource',
                'props' => new MenuConfigurationData(
                    version: '1.0',
                    meta: new MetaData(
                        resource: 'Role',
                        translationKey: 'role.list_title',
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
                        actions: ['create', 'view', 'edit', 'delete'],
                        rules: new ValidationRulesData([])
                    ),
                    roles: []
                )
            ]);
        }

        // Create Role create form
        MenuItem::create([
            'path' => '/user-management/roles/create',
            'name' => 'RoleCreate',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'Role',
                    translationKey: 'role.create_title',
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
                    actions: ['create'],
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
        ]);

        // Create Role edit form
        MenuItem::create([
            'path' => '/user-management/roles/:id/edit',
            'name' => 'RoleEdit',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'Role',
                    translationKey: 'role.edit_title',
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
                    actions: ['update'],
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
        ]);

        // Create Role view
        MenuItem::create([
            'path' => '/user-management/roles/:id',
            'name' => 'RoleShow',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'Role',
                    translationKey: 'role.show_title',
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
                    actions: ['view'],
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
        // Create Permission list view
        $permissionListItem = MenuItem::where('path', '/user-management/permissions')->first();

        if ($permissionListItem) {
            $permissionListItem->update([
                'component' => 'GenericResource',
                'props' => new MenuConfigurationData(
                    version: '1.0',
                    meta: new MetaData(
                        resource: 'Permission',
                        translationKey: 'permission.list_title',
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
                        actions: ['create', 'view', 'edit', 'delete'],
                        rules: new ValidationRulesData([])
                    ),
                    roles: []
                )
            ]);
        }

        // Create Permission create form
        MenuItem::create([
            'path' => '/user-management/permissions/create',
            'name' => 'PermissionCreate',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'Permission',
                    translationKey: 'permission.create_title',
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
                    actions: ['create'],
                    rules: new ValidationRulesData([
                        'create' => [
                            'name' => 'required|string|max:255|unique:permissions,name',
                        ]
                    ])
                ),
                roles: []
            )
        ]);

        // Create Permission edit form
        MenuItem::create([
            'path' => '/user-management/permissions/:id/edit',
            'name' => 'PermissionEdit',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'Permission',
                    translationKey: 'permission.edit_title',
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
                    actions: ['update'],
                    rules: new ValidationRulesData([
                        'update' => [
                            'name' => 'required|string|max:255|unique:permissions,name,{id}',
                        ]
                    ])
                ),
                roles: []
            )
        ]);
    }

    /**
     * Create the Organization resource configuration
     */
    private function createOrganizationResourceConfig(): void
    {
        // Create Organization list view
        $orgListItem = MenuItem::where('path', '/system/organizations')->first();

        if ($orgListItem) {
            $orgListItem->update([
                'component' => 'GenericResource',
                'props' => new MenuConfigurationData(
                    version: '1.0',
                    meta: new MetaData(
                        resource: 'Organization',
                        translationKey: 'organization.list_title',
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
                        actions: ['create', 'view', 'edit', 'delete'],
                        rules: new ValidationRulesData([])
                    ),
                    roles: [
                        'super-admin' => new RoleConfigurationData(
                            fields: [],
                            actions: ['create', 'view', 'edit', 'delete'],
                            rules: new ValidationRulesData([])
                        ),
                        'admin' => new RoleConfigurationData(
                            fields: [],
                            actions: ['view'],
                            rules: new ValidationRulesData([])
                        ),
                    ]
                )
            ]);
        }

        // Create Organization create form
        MenuItem::create([
            'path' => '/system/organizations/create',
            'name' => 'OrganizationCreate',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'Organization',
                    translationKey: 'organization.create_title',
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
                    actions: ['create'],
                    rules: new ValidationRulesData([
                        'create' => [
                            'name' => 'required|string|max:255',
                            'description' => 'nullable|string',
                        ]
                    ])
                ),
                roles: []
            )
        ]);

        // Create Organization edit form
        MenuItem::create([
            'path' => '/system/organizations/:id/edit',
            'name' => 'OrganizationEdit',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'Organization',
                    translationKey: 'organization.edit_title',
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
                    actions: ['update'],
                    rules: new ValidationRulesData([
                        'update' => [
                            'name' => 'required|string|max:255',
                            'description' => 'nullable|string',
                        ]
                    ])
                ),
                roles: []
            )
        ]);

        // Create Organization view
        MenuItem::create([
            'path' => '/system/organizations/:id',
            'name' => 'OrganizationShow',
            'component' => 'GenericForm',
            'props' => new MenuConfigurationData(
                version: '1.0',
                meta: new MetaData(
                    resource: 'Organization',
                    translationKey: 'organization.show_title',
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
                    actions: ['view'],
                    rules: new ValidationRulesData([])
                ),
                roles: []
            )
        ]);
    }
}
