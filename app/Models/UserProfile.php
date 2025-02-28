<?php

namespace App\Models;

use App\Contracts\Models\UserReadModel;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserProfile
 * @package App\Models
 * @property string $uuid
 * @property string $name
 * @property string $email
 * @property string|null $email_verified_at
 * @property array<int,Email>|null $emails
 */
class UserProfile extends Model implements UserReadModel
{
    /**
     * @var string
     */
    protected $connection = 'mariadb';

    /**
     * @var string
     */
    protected $table = 'user_profiles';

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
