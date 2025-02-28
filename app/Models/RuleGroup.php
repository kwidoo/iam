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

/**
 * Class RuleGroup
 *
 * @package App\Models
 * @property int|null $parent_id
 * @property int $id
 * @property string $uuid
 * @property RuleGroupType $type
 * @property string $entity_type
 * @property string|null $entity_uuid
 * @property string|null $user_uuid
 * @property string|null $description
 * @property int $_lft
 * @property int $_rgt
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Kalnoy\Nestedset\Collection<int, RuleGroup> $children
 * @property-read int|null $children_count
 * @property-read RuleGroup|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rule> $rules
 * @property-read int|null $rules_count
 * @method static \Kalnoy\Nestedset\Collection<int, static> all($columns = ['*'])
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup ancestorsAndSelf($id, array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup ancestorsOf($id, array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup applyNestedSetScope(?string $table = null)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup countErrors()
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup d()
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup defaultOrder(string $dir = 'asc')
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup descendantsAndSelf($id, array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup descendantsOf($id, array $columns = [], $andSelf = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup fixSubtree($root)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup fixTree($root = null)
 * @method static \Kalnoy\Nestedset\Collection<int, static> get($columns = ['*'])
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup getNodeData($id, $required = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup getPlainNodeData($id, $required = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup getTotalErrors()
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup hasChildren()
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup hasParent()
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup isBroken()
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup leaves(array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup makeGap(int $cut, int $height)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup moveNode($key, $position)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup newModelQuery()
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RuleGroup onlyTrashed()
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup orWhereAncestorOf(bool $id, bool $andSelf = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup orWhereDescendantOf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup orWhereNodeBetween($values)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup orWhereNotDescendantOf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup query()
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup rebuildSubtree($root, array $data, $delete = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup rebuildTree(array $data, $delete = false, $root = null)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup reversed()
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup root(array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereAncestorOf($id, $andSelf = false, $boolean = 'and')
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereAncestorOrSelf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereCreatedAt($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereDeletedAt($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereDescendantOf($id, $boolean = 'and', $not = false, $andSelf = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereDescendantOrSelf(string $id, string $boolean = 'and', string $not = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereDescription($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereEntityType($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereEntityUuid($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereId($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereIsAfter($id, $boolean = 'and')
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereIsBefore($id, $boolean = 'and')
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereIsLeaf()
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereIsRoot()
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereLft($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereNodeBetween($values, $boolean = 'and', $not = false, $query = null)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereNotDescendantOf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereParentId($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereRgt($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereType($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereUpdatedAt($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereUserUuid($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup whereUuid($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup withDepth(string $as = 'depth')
 * @method static \Illuminate\Database\Eloquent\Builder|RuleGroup withTrashed()
 * @method static \Kalnoy\Nestedset\QueryBuilder|RuleGroup withoutRoot()
 * @method static \Illuminate\Database\Eloquent\Builder|RuleGroup withoutTrashed()
 * @method static \Kalnoy\Nestedset\Collection<int, static> all($columns = ['*'])
 * @method static \Kalnoy\Nestedset\Collection<int, static> get($columns = ['*'])
 * @mixin \Eloquent
 */
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
     * The attributes that are mass assignable.
     * @var array<int,string>
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
     * The attributes that should be cast.
     * @var array<string,string>
     */
    protected $casts = [
        'type' => RuleGroupType::class,
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array<int,string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return HasMany<Rule>
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
