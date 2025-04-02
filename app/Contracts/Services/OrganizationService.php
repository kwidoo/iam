<?php

namespace App\Contracts\Services;

use App\Data\RegistrationData;
use App\Models\Organization;
use Kwidoo\Mere\Contracts\BaseService;

interface OrganizationService extends BaseService
{
    public function createDefaultForUser(RegistrationData $data): Organization;
    public function loadDefault(RegistrationData $data): Organization;
    public function createInitialOrganization(RegistrationData $data): Organization;
}
