<?php

namespace App\Aggregates\Manage;

use App\Data\Create\PhoneData;
use App\Events\Phone\PhoneCreated;

trait ManagePhones
{
    /**
     * @param PhoneData $phoneData
     *
     * @return self
     */
    public function createPhone(PhoneData $phoneData): self
    {
        $this->recordThat(
            (new PhoneCreated($phoneData))
                ->setMetaData([
                    'reference_id' => $phoneData->referenceId
                ])
        );

        return $this;
    }
}
