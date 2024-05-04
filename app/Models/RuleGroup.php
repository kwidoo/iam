<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class RuleGroup extends Model
{
    use HasFactory;
    use NodeTrait;
    use SoftDeletes;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'id',
        'parent_id',
        'uuid',
        'operator',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
        'operator' => 'string',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return BelongsToMany
     */
    public function rules(): HasMany
    {
        return $this->hasMany(
            Rule::class,
            'rule_group_uuid',
            'uuid'
        )->orderBy('order', 'asc');
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
