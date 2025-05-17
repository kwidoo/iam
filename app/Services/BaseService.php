<?php

namespace App\Services;

use Kwidoo\Mere\Services\BaseService as DefaultBaseService;

/**
 * Abstract base service providing common CRUD operations with lifecycle events.
 *
 * This class is an adaptation of Kwidoo\Mere\Services\BaseService that works with
 * the new kwidoo/lifecycle package instead of the old Lifecycle implementation.
 *
 * @package App\Services\Base
 */
abstract class BaseService extends DefaultBaseService
{
    //
}
