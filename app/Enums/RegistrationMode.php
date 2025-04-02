<?php

namespace App\Enums;

enum RegistrationMode: string
{
    case INVITE_ONLY = 'invite_only';
    case OPEN = 'open';

    public function isInviteOnly(): bool
    {
        return $this === self::INVITE_ONLY;
    }

    public function isOpen(): bool
    {
        return $this === self::OPEN;
    }
}
