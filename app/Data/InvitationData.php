<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class InvitationData extends Data
{
    public function __construct(
        public string $method,
        public string $value,
        public string $organizationId,
        public ?string $role = null,
        public ?array $meta = [],
    ) {}
}
