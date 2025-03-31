<?php

namespace App\Transformers\SuperAdmin;

use League\Fractal\TransformerAbstract;
use App\Models\Profile;

/**
 * Class UserTransformer.
 *
 * @package namespace App\Transformers;
 */
class ProfileTransformer extends TransformerAbstract
{
    public function __construct() {}

    /**
     * Transform the User entity.
     *
     * @param \App\Models\Profile $model
     *
     * @return array
     */
    public function transform(Profile $profile)
    {
        return [
            'id' => $profile->id,
            'fname' => $profile->fname,
            'lname' => $profile->lname,
            'dob'  =>   $profile->dob,
            'gender' => $profile->gender,
            'primary_contact' => $profile->primaryContact->uuid,
        ];
    }
}
