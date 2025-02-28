<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property string $uuid
 * @property string $user_uuid
 * @property string|null $last_used_at
 * @property string|null $revoked_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ApiToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiToken whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiToken whereRevokedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiToken whereUserUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiToken whereUuid($value)
 * @mixin \Eloquent
 */
class ApiToken extends Model
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
     * @var array<int, string>
     */
    protected $fillable = [
        'user_uuid',
        'last_used_at',
        'revoked_at',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'last_used_at',
        'revoked_at',
    ];
}
