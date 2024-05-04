<?php

namespace App\Models;

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
        'type',
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
        'operator' => 'string',
        'conditions' => 'object',
    ];

    /**
     * @return BelongsToMany
     */
    public function ruleGroup(): BelongsTo
    {
        return $this->belongsTo(RuleGroup::class, 'rule_group_uuid');
    }

    /**
     * @param string $value
     *
     * @return void
     */
    public function setOperatorAttribute(string|null $value): void
    {
        $this->attributes['operator'] = $value ? Str::upper($value) : null;
    }
}
