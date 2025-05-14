<?php

namespace App\Models;

use App\Traits\HasOrganizationPermissions;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Kwidoo\Contacts\Contracts\Contactable;
use Kwidoo\Contacts\Traits\HasContacts;
use Kwidoo\Mere\Contracts\Models\UserInterface;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @method static \Kwidoo\Mere\Contracts\Models\UserInterface create(array $data)
 * @method array getFillable()
 */
class User extends Authenticatable implements Contactable, UserInterface
{
    use HasFactory, Notifiable, HasApiTokens;
    use HasUuids;
    use SoftDeletes;
    use HasContacts;
    use HasRoles;
    use HasOrganizationPermissions;

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
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
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
     * @return HasMany<ApiToken>
     */
    public function iam_token(): HasMany
    {
        return $this->hasMany(ApiToken::class, 'user_id', 'id')->whereRevokedAt(null)->latest();
    }

    /**
     * @return HasMany<Organization>
     */
    public function ownedOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'owner_id', 'id');
    }


    /**
     * @return BelongsToMany
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * @return HasOne<Profile>
     * See migrations for lock on HasMany
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'user_id', 'id');
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function hasRoleInOrg(string $roleName, string $organizationId): bool
    {
        return DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_id', $this->id)
            ->where('model_has_roles.model_type', self::class)
            ->where('model_has_roles.organization_id', $organizationId)
            ->where('roles.name', $roleName)
            ->exists();
    }

    /**
     * Check if the user has authorized the client with the specified scopes.
     *
     * @param string $clientId
     * @param array|null $scopes
     * @return bool
     */
    public function hasAuthorizedClient(string $clientId, ?array $scopes = null): bool
    {
        $approval = ClientUserApproval::where('user_id', $this->id)
            ->where('client_id', $clientId)
            ->where('remembered', true)
            ->first();

        if (!$approval) {
            return false;
        }

        // If no specific scopes are requested, just check if the client is approved
        if ($scopes === null) {
            return true;
        }

        // Check if all requested scopes are approved
        return $approval->hasScopes($scopes);
    }

    /**
     * Get all client approvals for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clientApprovals()
    {
        return $this->hasMany(ClientUserApproval::class);
    }
}
