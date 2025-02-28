<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class PrimaryPhoneRule implements ValidationRule
{
    protected string $fullPhone;

    public function __construct(string $fullPhone)
    {
        $this->fullPhone = $fullPhone;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Find the existing record with the given full_phone
        $phoneRecord = DB::table('users') // Change 'users' to your actual table name
            ->where('full_phone', $this->fullPhone)
            ->first();

        // If full_phone exists but is_primary is not true in the database, fail validation
        if ($phoneRecord && !$phoneRecord->is_primary) {
            $fail('The phone number exists in the database, but it is not set as primary.');
        }
    }
}
