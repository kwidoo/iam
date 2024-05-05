<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EventSourcing\Projections\Projection;
use Spatie\Permission\Traits\HasRoles;

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
