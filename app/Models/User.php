<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Contracts\Models\UserReadModel;
use App\Contracts\UserResolver;
use Illuminate\Database\Eloquent\Builder;
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
 *
 * @package App\Models
 * @property string $has_primary_email
 * @property string $uuid
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Client> $clients
 * @property-read int|null $clients_count
 * @property-read \App\Models\Email|null $email
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Email> $emails
 * @property-read int|null $emails_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApiToken> $iam_token
 * @property-read int|null $iam_token_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Organization> $organizations
 * @property-read int|null $organizations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Phone|null $phone
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Profile> $profiles
 * @property-read int|null $profiles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Token> $tokens
 * @property-read int|null $tokens_count
 * @method static Builder|User byEmail(string $email)
 * @method static Builder|User byPhone(string $phone)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User onlyTrashed()
 * @method static Builder|User permission($permissions, $without = false)
 * @method static Builder|User query()
 * @method static Builder|User role($roles, $guard = null, $without = false)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUuid($value)
 * @method static Builder|User withTrashed()
 * @method static Builder|User withoutPermission($permissions)
 * @method static Builder|User withoutRole($roles, $guard = null)
 * @method static Builder|User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends Authenticatable implements UserReadModel
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
     *
     * @param string $uuid
     *
     * @return User
     */
    public function findForPassport(string $identifier): ?User
    {
        $resolver = app()->make(UserResolver::class);

        return $resolver->resolve($identifier);
    }

    public function validateForPassportPasswordGrant(string $password): bool
    {
        dump($this);
        return true;
    }

    /**
     * @return HasMany<Email>
     */
    public function emails(): HasMany
    {
        return $this->hasMany(Email::class, 'user_uuid', 'uuid');
    }

    /**
     * @return HasOne<Email>
     */
    public function email(): HasOne
    {
        return $this->hasOne(Email::class, 'user_uuid', 'uuid')->ofMany([
            'is_primary' => 'max'
        ], function ($query) {
            $query->where('is_primary', true)->orWhere(['is_primary' => 0, 'updated_at' => 'min']);
        });
    }

    /**
     * @return HasOne<Phone>
     */
    public function phone(): HasOne
    {
        return $this->hasOne(Phone::class, 'user_uuid', 'uuid')->ofMany([
            'created_at' => 'max'
        ]);
    }

    /**
     * @return HasMany<ApiToken>
     */
    public function iam_token(): HasMany
    {
        return $this->hasMany(ApiToken::class, 'user_uuid', 'uuid')->whereRevokedAt(null)->latest();
    }

    /**
     * @return HasMany<Organization>
     */
    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'user_uuid', 'uuid');
    }

    /**
     * @return bool
     */
    public function hasVerifiedEmail(): bool
    {
        if (config('iam.user_field') === 'phone') {
            return true;
        }
        return $this->email?->hasVerifiedEmail() ?? false;
    }

    /**
     * @return bool
     */
    public function getHasPrimaryEmailAttribute(): bool
    {
        return $this->email?->is_primary ?? false;
    }

    /**
     * @param Builder<User> $query
     * @param string $email
     *
     * @return void
     */
    public function scopeByEmail(Builder $query, string $email): void
    {
        $query->whereHas('emails', function (Builder $query) use ($email) {
            $query->where('emails.email', $email);
        });
    }

    /**
     * @param Builder<User> $query
     * @param string $phone
     *
     * @return void
     */
    public function scopeByPhone(Builder $query, string $phone): void
    {
        $query->whereHas('phone', function (Builder $query) use ($phone) {
            $query->where('phones.full_phone', $phone);
        });
    }
}
