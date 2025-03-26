<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public function __invoke()
    {
        return [
            "en" => [
                "top" => [
                    "profile" => "Profile",
                    "logout" => "Logout",
                ],
                "menu" => [
                    "properties" => "Properties",
                    "tenants" => "Tenants",
                    "landlords" => "Landlords",
                    "leaseAgreements" => "Lease Agreements",
                    "transactions" => "Transactions",
                    "profile" => "Profile",
                    "logout" => "Logout",
                    "organizations" => "Organizations",
                    "users" => "Users",
                    "roles" => "Roles",
                    "permissions" => "Permissions",
                ],
                "properties" => [
                    "header" => "Properties List",
                    "createNew" => "Create Property",
                    "edit" => "Edit Property",
                    "name" => "Name",
                    "address" => "Address",
                    "city" => "City",
                    "state" => "State",
                    "zip" => "ZIP Code",
                    "country" => "Country",
                    "description" => "Description",
                    "owner" => "Owner",
                    "tenants" => "Tenants",
                    "id" => "ID",
                    "type" => "Type",
                    "price" => "Price",
                ],
                "tenants" => [
                    "header" => "Tenants List",
                    "createNew" => "Create Tenant",
                    "edit" => "Edit Tenant",
                    "name" => "Name",
                    "email" => "Email",
                    "phoneNumber" => "Phone Number",
                    "id" => "ID",
                ],

                "common" => [
                    "new" => "New",
                    "save" => "Save",
                    "cancel" => "Cancel",
                    "delete" => "Delete",
                    "edit" => "Edit",
                    "create" => "Create",
                    "add" => "Add",
                    "close" => "Close",
                    "confirm" => "Confirm",
                    "next" => "Next",
                    "prev" => "Previous",
                    "search" => "Search",
                    "searchPlaceholder" => "Search...",
                    "page" => "Page",
                    "actions" => "Actions",
                    "loading" => "Loading...",
                ]
            ],
            "ru" => [
                "top" => [
                    "profile" => "Профиль",
                    "logout" => "Выйти",
                ],
                "menu" => [
                    "properties" => "Недвижимость",
                    "tenants" => "Арендаторы",
                    "landlords" => "Арендодатели",
                    "leaseAgreements" => "Договоры аренды",
                    "transactions" => "Транзакции",
                    "profile" => "Профиль",
                    "logout" => "Выйти",
                    "organization" => "Организации",
                    "user" => "Пользователи",
                    "role" => "Роли",
                    "permission" => "Права",
                ],
                "organizations" => [
                    "header" => "Список организаций",
                    "createNew" => "Создать организацию",
                    "createHeader" => "Создание организации",
                    "editHeader" => "Редактирование организации",
                    "submit" => "{type}",
                    "cancel" => "Отмена",
                    "edit" => "Редактировать организацию",
                    "create" => "Создать организацию",
                    "name" => "Название",
                    "description" => "Описание",
                    "logo" => "Логотип",
                    "id" => "ID",
                    "slug" => "Slug",
                    "ownerId" => "Владелец",

                ],
                "users" => [
                    "header" => "Список пользователей",
                    "createNew" => "Создать пользователя",
                    "edit" => "Редактировать пользователя",
                    "name" => "Имя",
                    "email" => "Email",
                    "phoneNumber" => "Телефон",
                    "id" => "ID",
                ],
                "properties" => [
                    "header" => "Список недвижимости",
                    "createNew" => "Создать недвижимость",
                    "edit" => "Редактировать недвижимость",
                    "name" => "Название",
                    "address" => "Адрес",
                    "addressLine1" => "Адрес (1)",
                    "addressLine2" => "Адрес (2)",

                    "city" => "Город",
                    "state" => "Штат",
                    "zipCode" => "Почтовый индекс",
                    "country" => "Страна",
                    "description" => "Описание",
                    "owner" => "Владелец",
                    "tenants" => "Арендаторы",
                    "id" => "ID",
                    "type" => "Тип",
                    "price" => "Цена",
                ],
                "tenants" => [
                    "header" => "Список арендаторов",
                    "createNew" => "Создать арендатора",
                    "edit" => "Редактировать арендатора",
                    "name" => "Имя",
                    "email" => "Email",
                    "phoneNumber" => "Телефон",
                    "id" => "ID",
                ],
                "common" => [
                    "new" => "Новый",
                    "save" => "Сохранить",
                    "cancel" => "Отмена",
                    "delete" => "Удалить",
                    "edit" => "Редактировать",
                    "create" => "Создать",
                    "add" => "Добавить",
                    "close" => "Закрыть",
                    "confirm" => "Подтвердить",
                    "next" => "Далее",
                    "prev" => "Назад",
                    "search" => "Поиск",
                    "searchPlaceholder" => "Поиск...",
                    "page" => "Страница",
                    "actions" => "Действия",
                    "loading" => "Загрузка...",
                ],
                "landlords" => [
                    "header" => "Список арендодателей",
                    "createNew" => "Создать арендодателя",
                    "name" => "Имя",
                    "email" => "Email",
                    "phoneNumber" => "Телефон",
                    "id" => "ID",

                ]
            ]


        ];
    }
}
