<?php

namespace App\Enums;

enum RuleGroupType: string
{
    case create = 'CREATE';
    case read = 'READ';
    case update = 'UPDATE';
    case delete = 'DELETE';
    case restore = 'RESTORE';
    case forceDelete = 'FORCE_DELETE';

    case inherit = 'INHERIT';
}
