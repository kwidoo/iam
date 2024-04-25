<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Events\User\UserCreated;
use App\Traits\UserEventsTrait;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    use HasUlids;
    use SoftDeletes;
    use UserEventsTrait;


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
            'password' => 'hashed',

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
    public function profile(): HasMany
    {
        return $this->hasMany(Profile::class, 'user_uuid', 'uuid');
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
            $query->where('is_primary', true)->orWhere(['is_primary' => 0, 'updated_at' => 'max']);
        });
    }


    public function hasVerifiedEmail(): bool
    {
        return $this->email?->hasVerifiedEmail();
    }
}
