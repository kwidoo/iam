<?php

namespace App\Transformers;

use App\Factories\ProfileOrganizationFactory;
use League\Fractal\TransformerAbstract;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProfileTransformer.
 *
 * @package namespace App\Transformers;
 */
class ProfileTransformer extends TransformerAbstract
{
    public function __construct(protected ProfileOrganizationFactory $factory) {}
    /**
     * Transform the Profile entity.
     *
     * @param \App\Models\Profile $model
     *
     * @return array
     */
    public function transform(Model $user)
    {
        $model = $user->profile;
        $organization = $this->factory->make('main');
        $organizations = [];
        if ($organization) {
            $organizations =
                [
                    'organization_name' => $organization->name,
                    'organization_id' => $organization->id,
                ];
        }
        return [
            'id'         => $model->id,
            'user_id'    => $model->user_id,
            'fname'     => $model->fname,
            'lname'     => $model->lname,
            'dob' => $model->dob,
            'gender' => $model->gender,
            ...$organizations,
            $model->primaryContact->type => $model->primaryContact->value,
        ];
    }
}
