<?php

namespace App\Aggregates\Manage;

use App\Data\Create\ProfileData;
use App\Events\Profile\ProfileCreated;

trait ManageProfiles
{
    /**
     * @param ProfileData $profileData
     *
     * @return self
     */
    public function createProfile(ProfileData $profileData): self
    {
        $this->recordThat(
            (new ProfileCreated($profileData))
                ->setMetaData([
                    'reference_id' => $profileData->referenceId
                ])
        );

        return $this;
    }
}
