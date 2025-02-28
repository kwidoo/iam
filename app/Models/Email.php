<?php

namespace App\Models;

use App\Contracts\Models\UseForAuthentication;
use App\Models\Traits\BelongsToUser;
use Illuminate\Auth\MustVerifyEmail as AuthMustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\EventSourcing\Projections\Projection;

/**
 * Class Email
 *
 * @package App\Models
 * @property string $email
 * @property string $uuid
 * @property string $user_uuid
 * @property bool $is_primary
 * @property array|null $data
 * @property string|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $email_for_unique
 * @property string|null $user_uuid_for_primary
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Email isPrimary()
 * @method static \Illuminate\Database\Eloquent\Builder|Email isVerified()
 * @method static \Illuminate\Database\Eloquent\Builder|Email newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Email newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Email onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Email query()
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereEmailForUnique($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereUserUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereUserUuidForPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Email withoutTrashed()
 * @mixin \Eloquent
 */
class Email extends Projection implements MustVerifyEmail, UseForAuthentication
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    use AuthMustVerifyEmail;
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
        'email',
        'user_uuid',
        'is_primary',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    /**
     * @param Builder $query
     *
     * @return void
     */
    public function scopeIsVerified(Builder $query): void
    {
        $query->whereNotNull('email_verified_at');
    }

    /**
     * @param Builder $query
     *
     * @return void
     */
    public function scopeIsPrimary(Builder $query): void
    {
        $query->where('is_primary', true);
    }

    /**
     * @param string $email
     *
     * @return self|null
     */
    public static function findByEmail(string $email): ?self
    {
        return self::where('email', $email)->where('is_primary', true)->first();
    }
}
