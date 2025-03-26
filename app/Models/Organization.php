<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Exception;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;
    use HasUuids;
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
    protected $primaryKey = 'id';


    protected $fillable = [
        'id',
        'name',
        'slug',
        'owner_id',
        'description',
        'logo',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($org) {
            if (in_array($org->slug, ['main', 'admin', 'login'])) {
                throw new Exception("This slug is reserved.");
            }
        });
    }


    /** The user who created/owns the organization.
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Many-to-many relationship with users.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /** One-to-many relationship with invitations.
     *
     * @return HasMany
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * @return BelongsToMany
     */
    public function profiles(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class, 'organization_profile');
    }

    /**
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    /**
     * @return [type]
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
