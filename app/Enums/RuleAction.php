<?php

namespace App\Enums;

enum RuleAction: string
{
    case allow = 'ALLOW';
    case deny = 'DENY';
}
