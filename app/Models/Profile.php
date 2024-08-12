<?php

namespace App\Models;

use App\Data\Create\ProfileData;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EventSourcing\Projections\Projection;

/**
 * 
 *
 * @property string $uuid
 * @property string $name
 * @property string $type
 * @property string $user_uuid
 * @property string $organization_uuid
 * @property \Spatie\LaravelData\Contracts\BaseData|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $type_for_unique
 * @property-read \Kalnoy\Nestedset\Collection<int, \App\Models\RuleGroup> $rule_groups
 * @property-read int|null $rule_groups_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Profile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile query()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereOrganizationUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereTypeForUnique($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereUserUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile withoutTrashed()
 * @mixin \Eloquent
 */
class Profile extends Projection
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    use BelongsToUser;


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
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'user_uuid',
        'organization_uuid',
        'type',
        'data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => ProfileData::class,
    ];

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
        )->where('entity_type', $this->getMorphClass())->withPivotValue('entity_type', $this->getMorphClass());
    }
}
