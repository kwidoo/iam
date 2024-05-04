<?php

namespace App\Models;

use App\Data\ProfileData;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EventSourcing\Projections\Projection;

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

    protected $casts = [
        'data' => ProfileData::class,
    ];

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
        )->where('entity_type', $this->getMorphClass())->withPivotValue('entity_type', $this->getMorphClass());
    }
}
