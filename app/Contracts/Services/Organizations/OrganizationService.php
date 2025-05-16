<?php

namespace App\Contracts\Services\Organizations;

use App\Data\Organizations\OrganizationCreateData;
use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use Kwidoo\Mere\Contracts\Services\BaseService;

interface OrganizationService extends BaseService
{
    public function connect(OrganizationCreateData $data): OrganizationInterface;
    public function findBySlug(string $slug): ?OrganizationInterface;
}
