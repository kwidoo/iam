<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\EventSourcing\Projections\Projection;

/**
 * 
 *
 * @property string $uuid
 * @property string $country_code
 * @property string $phone
 * @property string $user_uuid
 * @property string|null $full_phone
 * @property array|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Phone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Phone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Phone onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Phone query()
 * @method static \Illuminate\Database\Eloquent\Builder|Phone whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phone whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phone whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phone whereFullPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phone wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phone whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phone whereUserUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phone whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phone withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Phone withoutTrashed()
 * @mixin \Eloquent
 */
class Phone extends Projection
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    use Notifiable;
    use BelongsToUser;

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
        'country_code',
        'phone',
        'user_uuid',
        'data',
    ];

    /** @var array<string,string> */
    protected $casts = [
        'data' => 'array',
    ];
}
