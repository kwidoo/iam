<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use App\Enums\InviteMethod;

class InvitationConfigData extends Data
{
    public function __construct(
        public InviteMethod $method
    ) {}

    public static function fromInvitation(InvitationData $data): self
    {
        return new self(InviteMethod::from($data->method));
    }
}
