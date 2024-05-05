<?php

namespace App\Rules\Data;

use Spatie\LaravelData\Data;

class RuleConditionData extends Data
{
    public function __construct(
        public string $type = 'entity_based',
        public string $comparison,
        public string $subject,
        /**
         * @var string|int|bool|array|null
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
