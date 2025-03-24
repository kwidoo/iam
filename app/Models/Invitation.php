<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Invitation extends Model
{
    use HasFactory;
    use HasUuids;


    // Use UUID as primary key.
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'organization_id',
        'invited_by',
        'contact_type',
        'contact_value',
        'token',
        'expires_at',
        'accepted_at',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-generate UUID and token on creation.
        static::creating(function ($model) {
            if (empty($model->token)) {
                $model->token = Str::random(32);
            }
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
