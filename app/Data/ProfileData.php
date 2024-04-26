<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ProfileData extends Data
{
    public function __construct(
        public string $uuid,
        public string $name,

    ) {
    }
}
