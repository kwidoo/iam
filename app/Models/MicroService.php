<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EventSourcing\Projections\Projection;
use Spatie\Permission\Traits\HasRoles;

/**
 * 
 *
 * @property string $uuid
 * @property string $name
 * @property string $slug
 * @property string $client_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MicroService findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|MicroService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MicroService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MicroService onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MicroService permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|MicroService query()
 * @method static \Illuminate\Database\Eloquent\Builder|MicroService role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|MicroService whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MicroService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MicroService whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MicroService whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MicroService whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MicroService whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MicroService whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MicroService withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MicroService withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|MicroService withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|MicroService withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|MicroService withoutTrashed()
 * @mixin \Eloquent
 */
class MicroService extends Projection
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    use HasRoles;

    // insert sluggable trait here
    use Sluggable;

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
        'name',
        'slug',
        'client_id',
    ];

    /**
     * @return array<string, mixed>
     */

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }
}
