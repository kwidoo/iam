<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Microservice.
 *
 * @package namespace App\Models;
 */
class Microservice extends Model implements Transformable
{
    use TransformableTrait;
    use HasFactory, HasUuids;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'microservices';
    protected $fillable = ['name', 'endpoint', 'api_key', 'status'];
}
