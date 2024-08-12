<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

/**
 *
 * Class UserProfile
 * @package App\Models
 * @property string $uuid
 * @property string $user_uuid
 * @property string $name
 * @property string $email
 * @property string|null $email_verified_at
 * @property array<int,Email>|null $emails
 */
class UserProfile extends Model
{
    /**
     * @var string
     */
    protected $connection = 'mongodb';

    /**
     * @var string
     */
    protected $collection = 'user_profiles';

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    protected $hidden = [
        '_id',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'uuid',
        'user_uuid',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'zip',
        'created_at',
        'updated_at',
    ];
}
