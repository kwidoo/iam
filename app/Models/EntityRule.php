<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $rule_group_id
 * @property string $entity_type
 * @property string $entity_uuid
 * @method static \Illuminate\Database\Eloquent\Builder|EntityRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityRule query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityRule whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityRule whereEntityUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityRule whereRuleGroupId($value)
 * @mixin \Eloquent
 */
class EntityRule extends Model
{
    use HasFactory;
}
