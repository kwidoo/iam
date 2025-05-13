<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class DeleteData extends Data
{
    public function __construct(
        public readonly string $id,
    ) {}
}
