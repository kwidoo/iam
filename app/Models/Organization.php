<?php

namespace App\Models;

use App\Data\OrganizationData;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EventSourcing\Projections\Projection;

class Organization extends Projection
{
    use HasFactory;
    use HasUuids;

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

    protected $fillable = [
        'uuid',
        'name',
        'user_uuid',
        'data',
    ];

    protected $casts = [
        'data' => OrganizationData::class,
    ];

    /**
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function owner_profile()
    {
        return $this->hasOne(
            Profile::class,
            'organization_uuid',
            'uuid'
        )->where('profiles.user_uuid', $this->user_uuid);
    }

    public function profiles()
    {
        return $this->hasMany(
            Profile::class,
            'organization_uuid',
            'uuid'
        );
    }

    /**
     * @return BelongsTo
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
