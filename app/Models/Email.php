<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use App\Traits\EmailEventsTrait;
use Illuminate\Auth\MustVerifyEmail as AuthMustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\EventSourcing\Projections\Projection;

class Email extends Projection implements MustVerifyEmail
{
    use HasFactory;
    use HasUuids;
    use EmailEventsTrait;
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
        'data',
    ];

    protected $casts = [
        // 'email' => 'encrypted',
        'is_primary' => 'boolean',
        'data' => 'array',
    ];

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
}
