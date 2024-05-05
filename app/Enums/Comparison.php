<?php

namespace App\Enums;

enum Comparison: string
{
    case is = 'is';
    case isNot = 'is_not';
    case contains = 'contains';
    case doesNotContain = 'does_not_contain';
    case isGreaterThan = 'is_greater_than';
    case isLessThan = 'is_less_than';
    case isGreaterThanOrEqualTo = 'is_greater_than_or_equal_to';
    case isLessThanOrEqualTo = 'is_less_than_or_equal_to';
    case isInArray = 'is_in_array';
    case isNotInArray = 'is_not_in_array';
    case isEmpty = 'is_empty';
    case isNotEmpty = 'is_not_empty';
    case isNull = 'is_null';
    case isNotNull = 'is_not_null';
    case isInstanceOf = 'is_instance_of';

    case isAfter = 'is_after';
    case isBefore = 'is_before';
    case isOnOrAfter = 'is_on_or_after';
    case isOnOrBefore = 'is_on_or_before';
}
