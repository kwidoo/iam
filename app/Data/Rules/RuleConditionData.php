<?php

namespace App\Data\Rules;

use Spatie\LaravelData\Data;

class RuleConditionData extends Data
{
    public function __construct(
        public string $comparison,
        public string $subject,
        public string $type = 'entity_based',
        /**
         * @var string|int|bool|array<string,string>|null
         */
        public $value = null,
    ) {
    }

    public static function from(mixed ...$payload): static
    {
        return parent::from([
            'type' => 'entity_based',
            ...$payload,
        ]);
    }
}
