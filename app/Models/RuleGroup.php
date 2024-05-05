<?php

namespace App\Models;

use App\Enums\RuleGroupType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;
use Kalnoy\Nestedset\NodeTrait;

class RuleGroup extends Model
{
    use HasFactory;
    use NodeTrait;
    use SoftDeletes;

    public static function boot()
    {
        parent::boot();

        static::saving(function (self $model) {
            if (!is_null($model->user_uuid) && is_null($model->parent_id)) {
                throw ValidationException::withMessages([
                    'user_uuid' => 'Cannot set user_uuid when parent_id is NULL.'
                ]);
            }
            if (!is_null($model->entity_uuid) && is_null($model->parent_id)) {
                throw ValidationException::withMessages([
                    'entity_uuid' => 'Cannot set entity_uuid when parent_id is NULL.'
                ]);
            }
        });
    }

    /**
     * @var array<string>
     */
    protected $fillable = [
        'id',
        'uuid',
        'type',
        'entity_type',
        'entity_uuid',
        'parent_id',
    ];

    /**
     * @var array<string>
     */
    protected $casts = [
        'type' => RuleGroupType::class,
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
        )->orderBy('order', 'ASC');
    }
}
