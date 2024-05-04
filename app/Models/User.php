<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 * @package App\Models
 *
 * @property string $has_primary_email
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    use HasUuids;
    use SoftDeletes;
    use HasRoles;

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
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            //
        ];
    }

    /**
     * Find the user instance for the given username.
     */
    public function findForPassport(string $uuid): User
    {
        try {
            return $this->where('uuid', $uuid)->firstOrFail();
        } catch (\Exception $e) {
            abort(422, 'Incorrect user or password');
        }
    }

    /**
     * @return HasMany
     */
    public function emails(): HasMany
    {
        return $this->hasMany(Email::class, 'user_uuid', 'uuid');
    }

    /**
     * @return HasOne
     */
    public function email(): HasOne
    {
        return $this->hasOne(Email::class, 'user_uuid', 'uuid')->ofMany([
            'is_primary' => 'max'
        ], function ($query) {
            $query->where('is_primary', true)->orWhere(['is_primary' => 0, 'updated_at' => 'min']);
        });
    }

    public function iam_token()
    {
        return $this->hasMany(ApiToken::class, 'user_uuid', 'uuid')->whereRevokedAt(null)->latest();
    }


    /**
     * @return bool
     */
    public function hasVerifiedEmail(): bool
    {
        return $this->email?->hasVerifiedEmail();
    }

    /**
     * @return bool
     */
    public function getHasPrimaryEmailAttribute(): bool
    {
        return $this->email?->is_primary;
    }

    public function organizations()
    {
        return $this->hasMany(Organization::class, 'user_uuid', 'uuid');
    }

    public function profiles()
    {
        return $this->hasMany(Profile::class, 'user_uuid', 'uuid');
    }
}
