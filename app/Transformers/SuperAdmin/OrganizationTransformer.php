<?php

namespace App\Transformers\SuperAdmin;

use League\Fractal\TransformerAbstract;
use App\Models\Organization;

/**
 * Class UserTransformer.
 *
 * @package namespace App\Transformers;
 */
class OrganizationTransformer extends TransformerAbstract
{
    public function __construct() {}

    /**
     * Transform the User entity.
     *
     * @param \App\Models\User $model
     *
     * @return array
     */
    public function transform(Organization $organization)
    {
        return [
            'id' => $organization->id,
            'name' => $organization->name,
            'profile' => (new ProfileTransformer())->transform($organization->profiles()->first()),


        ];
    }
}
