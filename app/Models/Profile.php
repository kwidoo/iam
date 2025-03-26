<?php

namespace App\Models;

use App\Models\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Profile.
 *
 * @package namespace App\Models;
 */
class Profile extends Model implements Transformable
{
    use HasFactory;
    use HasUuids;
    use TransformableTrait;
    use BelongsToUser;
    use SoftDeletes;


    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'fname',
        'lname',
        'dob',
        'gender',
    ];

    /**
     * @return HasMany
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(
            config('contacts.model'),
            'contactable_id',
            'user_id'
        )->where('contacts.contactable_type', (new User)->getMorphClass());
    }

    /**
     * @return HasOne
     */
    public function primaryContact(): HasOne
    {
        return $this->hasOne(config('contacts.model'), 'contactable_id', 'user_id')
            ->where('contactable_type', (new User)->getMorphClass())
            ->ofMany('is_primary', 'max');
    }

    /**
     * @return BelongsTo
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_profile');
    }
}
