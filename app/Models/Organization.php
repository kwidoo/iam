<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Exception;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    protected $primaryKey = 'uuid';


    protected $fillable = [
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


    // The user who created/owns the organization.
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Many-to-many relationship with users.
    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    // One-to-many relationship with invitations.
    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
