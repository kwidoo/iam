<?php

namespace App\Services\Organizations;

use App\Enums\OrganizationFlow;
use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use Spatie\LaravelData\Contracts\BaseData;

class MainOnly extends UserJoinsUserOrg
{
    /**
     * Get the registration flow type this strategy handles.
     *
     * @return OrganizationFlow
     */
    public function key(): OrganizationFlow
    {
        return OrganizationFlow::MAIN_ONLY;
    }

    /**
     * Load the default organization for the user during registration.
     * Associates the user with the main organization if it exists.
     *
     * @param \App\Data\Organizations\OrganizationCreateData $data Registration data containing user and org info
     *
     * @return \App\Models\Organization
     */
    public function create(BaseData $data): OrganizationInterface
    {
        $data->name = 'Main organization';
        $data->slug = 'main';
        $data->flow =  $this->key();

        return parent::create($data);
    }
}
