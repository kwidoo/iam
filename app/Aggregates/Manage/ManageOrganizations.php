<?php

namespace App\Aggregates\Manage;

use App\Data\Create\OrganizationData;
use App\Events\Organization\OrganizationCreated;

trait ManageOrganizations
{
    /**
     * @param OrganizationData $organizationData
     *
     * @return self
     */
    public function createOrganization(OrganizationData $organizationData): self
    {
        $this->recordThat(
            (new OrganizationCreated($organizationData))
                ->setMetaData([
                    'reference_id' => $organizationData->referenceId
                ])
        );

        return $this;
    }
}
