<?php

namespace App\Models;

use App\Enums\RuleAction;
use App\Enums\RuleOperator;
use App\Rules\Data\RuleConditionData;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EventSourcing\Projections\Projection;

/**
 * Class Rule
 *
 * @package App\Models
 * @property string $description
 * @property RuleAction $action
 * @property RuleOperator $operator
 * @property RuleConditionData $conditions
 * @property int $order
 * @property string $rule_group_uuid
 * @property string $uuid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\RuleGroup|null $ruleGroup
 * @method static \Illuminate\Database\Eloquent\Builder|Rule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rule query()
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereConditions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereOperator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereRuleGroupUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereUuid($value)
 * @mixin \Eloquent
 */
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'rule_group_uuid',
        'description',
        'action',
        'conditions',
        'operator',
        'order',
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     * @var array<string, string>
     */
    protected $casts = [
        'order' => 'integer',
        'action' => RuleAction::class,
        'operator' => RuleOperator::class,
        'conditions' => RuleConditionData::class,
    ];

    /**
     * @return BelongsTo<RuleGroup,self>
     */
    public function ruleGroup(): BelongsTo
    {
        return $this->belongsTo(RuleGroup::class, 'rule_group_uuid');
    }
}
