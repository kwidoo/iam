<?php

namespace App\Resolvers;

use App\Data\AccessAssignmentData;
use App\Enums\RegistrationFlow;
use Kwidoo\Mere\Contracts\AccessAssignmentContextResolver;
use Kwidoo\Mere\Contracts\AccessAssignmentData as AccessAssignmentDataContract;
use Spatie\LaravelData\Contracts\BaseData;

class GuestRegistrationContextResolver implements AccessAssignmentContextResolver
{
    /**
     * @param \App\Data\RegistrationData $data
     *
     * @return AccessAssignmentDataContract
     */
    public function resolve(BaseData $data): AccessAssignmentDataContract
    {
        return new AccessAssignmentData(
            actor: 'self',
            user: $data->user,
            flow: RegistrationFlow::from($data->flow),
        );
    }
}
