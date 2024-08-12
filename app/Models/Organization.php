<?php

namespace App\Models;

use App\Data\Create\OrganizationData;
use App\Traits\HasManyProfiles;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\EventSourcing\Projections\Projection;


/**
 *
 *
 * @property string $uuid
 * @property string $user_uuid
 * @property string $name
 * @property \Spatie\LaravelData\Contracts\BaseData|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $owner
 * @property-read \App\Models\Profile|null $owner_profile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Profile> $profiles
 * @property-read int|null $profiles_count
 * @property-read \Kalnoy\Nestedset\Collection<int, \App\Models\RuleGroup> $rule_groups
 * @property-read int|null $rule_groups_count
 * @method static \Illuminate\Database\Eloquent\Builder|Organization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Organization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Organization query()
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereUserUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereUuid($value)
 * @mixin \Eloquent
 */
class Organization extends Projection
{
    use HasFactory;
    use HasUuids;
    use HasManyProfiles;

    /**
     * @var string
     */
    protected $table = 'organizations';

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
    protected $primaryKey = 'uuid';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'user_uuid',
        'data',
    ];

    /**
     * @var array<string,string>
     */
    protected $casts = [
        'data' => OrganizationData::class,
    ];

    /**
     * @return BelongsTo<User,self>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    /**
     * @return HasOne<Profile>
     */
    public function owner_profile(): HasOne
    {
        return $this->hasOne(
            Profile::class,
            'organization_uuid',
            'uuid'
        )->where('profiles.user_uuid', $this->user_uuid);
    }

    /**
     * @return BelongsToMany<RuleGroup>
     */
    public function rule_groups(): BelongsToMany
    {
        return $this->belongsToMany(
            RuleGroup::class,
            'entity_rules',
            'entity_uuid',
            'rule_group_id'
        )->where(
            'entity_type',
            $this->getMorphClass()
        )->withPivotValue(
            'entity_type',
            $this->getMorphClass()
        );
    }
}
