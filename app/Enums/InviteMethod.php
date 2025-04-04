<?php

namespace App\Enums;

enum InviteMethod: string
{
    case EMAIL = 'email';
    case PHONE = 'sms';
    case EXISTING_USER = 'existing_user';
}
