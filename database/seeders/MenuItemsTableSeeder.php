<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuItemsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('menu_items')->delete();

        DB::table('menu_items')->insert(array(
            0 =>
            array(
                'id' => 1,
                'path' => '/organizations',
                'name' => 'OrganizationList',
                'component' => 'GenericResource',
                'redirect' => NULL,
                'props' => '{"label":"organizations","fields":[{"key":"name","label":"Name","sortable":true,"visible":true},{"key":"slug","label":"Slug","sortable":true,"visible":true},{"key":"owner_id","label":"Owner Id","sortable":true,"visible":true},{"key":"description","label":"Description","sortable":true,"visible":true},{"key":"logo","label":"Logo","sortable":true,"visible":true}],"actions":{"create":true,"edit":true,"delete":true}}',
                'parent_id' => NULL,
                '_lft' => 1,
                '_rgt' => 2,
                'created_at' => '2025-03-24 16:38:35',
                'updated_at' => '2025-03-24 16:38:35',
                'deleted_at' => NULL,
            ),
            1 =>
            array(
                'id' => 2,
                'path' => '/organizations/create',
                'name' => 'OrganizationCreate',
                'component' => 'GenericCreate',
                'redirect' => NULL,
                'props' => '{"label":"organizations\/create","fields":[{"key":"name","label":"Name","sortable":true,"visible":true},{"key":"slug","label":"Slug","sortable":true,"visible":true},{"key":"owner_id","label":"Owner Id","sortable":true,"visible":true},{"key":"description","label":"Description","sortable":true,"visible":true},{"key":"logo","label":"Logo","sortable":true,"visible":true}],"rules":{"name":["required","string","max:255"],"description":["nullable","string"],"logo":["nullable","string"]}}',
                'parent_id' => NULL,
                '_lft' => 3,
                '_rgt' => 4,
                'created_at' => '2025-03-24 16:38:36',
                'updated_at' => '2025-03-24 16:38:36',
                'deleted_at' => NULL,
            ),
            2 =>
            array(
                'id' => 3,
                'path' => '/organizations/edit/:id',
                'name' => 'OrganizationUpdate',
                'component' => 'GenericUpdate',
                'redirect' => NULL,
                'props' => '{"label":"organizations\/edit\/:id","fields":[{"key":"name","label":"Name","sortable":true,"visible":true},{"key":"slug","label":"Slug","sortable":true,"visible":true},{"key":"owner_id","label":"Owner Id","sortable":true,"visible":true},{"key":"description","label":"Description","sortable":true,"visible":true},{"key":"logo","label":"Logo","sortable":true,"visible":true}],"rules":{"name":["sometimes","required","string","max:255"],"description":["nullable","string"],"logo":["nullable","string"]}}',
                'parent_id' => NULL,
                '_lft' => 5,
                '_rgt' => 6,
                'created_at' => '2025-03-24 16:38:36',
                'updated_at' => '2025-03-24 16:38:36',
                'deleted_at' => NULL,
            ),
            3 =>
            array(
                'id' => 4,
                'path' => '/users',
                'name' => 'UserList',
                'component' => 'GenericResource',
                'redirect' => NULL,
                'props' => '{"label":"users","fields":[{"key":"uuid","label":"Uuid","sortable":true,"visible":true},{"key":"password","label":"Password","sortable":true,"visible":true}],"actions":{"create":true,"edit":true,"delete":true}}',
                'parent_id' => NULL,
                '_lft' => 7,
                '_rgt' => 8,
                'created_at' => '2025-03-24 16:40:46',
                'updated_at' => '2025-03-24 16:40:46',
                'deleted_at' => NULL,
            ),
            4 =>
            array(
                'id' => 5,
                'path' => '/users/create',
                'name' => 'RegistrationCreate',
                'component' => 'GenericCreate',
                'redirect' => NULL,
                'props' => '{"label":"users\/create","fields":[{"key":"uuid","label":"Uuid","sortable":true,"visible":true},{"key":"password","label":"Password","sortable":true,"visible":true}],"rules":{"create":{"method":["required","in:\'email\',\phone\'\'"],"otp":["required","boolean"]}}}',
                'parent_id' => NULL,
                '_lft' => 9,
                '_rgt' => 10,
                'created_at' => '2025-03-24 16:40:46',
                'updated_at' => '2025-03-24 16:40:46',
                'deleted_at' => NULL,
            ),
            5 =>
            array(
                'id' => 6,
                'path' => '/users/edit/:id',
                'name' => 'UserUpdate',
                'component' => 'GenericUpdate',
                'redirect' => NULL,
                'props' => '{"label":"users\\/edit\\/:id","fields":[{"key":"uuid","label":"Uuid","sortable":true,"visible":true},{"key":"password","label":"Password","sortable":true,"visible":true}],"rules":[]}',
                'parent_id' => NULL,
                '_lft' => 11,
                '_rgt' => 12,
                'created_at' => '2025-03-24 16:40:46',
                'updated_at' => '2025-03-24 16:40:46',
                'deleted_at' => NULL,
            ),
        ));
    }
}
