<?php

namespace App\Traits;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasManyProfiles
{
    /**
     * Get the profiles for the model.
     *
     * @return HasMany<Profile,self>
     */
    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }
}
