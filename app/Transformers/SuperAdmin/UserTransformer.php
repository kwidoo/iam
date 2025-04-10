<?php

namespace App\Transformers\SuperAdmin;

use App\Contracts\Repositories\OrganizationRepository;
use League\Fractal\TransformerAbstract;
use App\Models\User;
use App\Models\Organization;

/**
 * Class UserTransformer.
 *
 * @package namespace App\Transformers;
 */
class UserTransformer extends TransformerAbstract
{
    public function __construct() {}

    /**
     * Transform the User entity.
     *
     * @param \App\Models\User $model
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'name' => $user->profile()->first()->full_name,
            'email' => $user->primaryContact->value,
            'createdAt' => $user->created_at,
            'login' => [
                'id' => $user->primaryContact->uuid,
                'value' => $user->primaryContact->value,
                'type' => $user->primaryContact->type,
            ],
            'organizations' => $user->organizations->map(fn($organization) => (new OrganizationTransformer())->transform($organization)),
            'roles' => $user->roles->map(fn($role) => ['id' => $role->id, 'value' => $role->name, 'name' => $role->name]),
            'contacts' => $user->contacts->map(fn($contact) => ['id' => $contact->uuid, 'value' => $contact->value, 'type' => $contact->type]),
            'resources' => [
                'organizations' => Organization::get()->map(fn($organization) => ['id' => $organization->id, 'name' => $organization->name]),
                'contacts' => $user->contacts->map(fn($contact) => ['id' => $contact->uuid, 'value' => $contact->value]),
                'roles' => $user->roles->map(fn($role) => [$role->id => $role->name]),
            ]
        ];
    }
}
