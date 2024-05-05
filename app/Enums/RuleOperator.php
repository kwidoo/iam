<?php

namespace App\Enums;

enum RuleOperator: string
{
    case and = 'AND';
    case or = 'OR';
    case not = 'NOT';
}
