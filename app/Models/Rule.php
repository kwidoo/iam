<?php

namespace App\Models;

use App\Enums\RuleAction;
use App\Enums\RuleOperator;
use App\Rules\Data\RuleConditionData;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\EventSourcing\Projections\Projection;
use Illuminate\Support\Str;

class Rule extends Projection
{
    use HasFactory;
    use HasUuids;

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
        'rule_group_uuid',
        'description',
        'action',
        'conditions',
        'operator',
        'order',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'order' => 'integer',
        'action' => RuleAction::class,
        'operator' => RuleOperator::class,
        'conditions' => RuleConditionData::class,
    ];

    /**
     * @return BelongsToMany
     */
    public function ruleGroup(): BelongsTo
    {
        return $this->belongsTo(RuleGroup::class, 'rule_group_uuid');
    }
}
