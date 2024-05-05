<?php

namespace App\Conditions;

use App\Enums\Comparison;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TimeBasedCondition
{
    protected Carbon $value;

    public function __construct(
        protected Comparison $comparison,
        protected string|null $subject = null,
        $value
    ) {
        $this->value = is_string($value) ? Carbon::parse($value) : $value;
    }

    /**
     * Evaluates based on a Model or DateTime object.
     *
     * @param Model|Carbon $entity
     * @param string|null $field
     * @return bool
     */
    public function evaluate($entity): bool
    {
        $comparisonMap = $this->getComparisonMap();

        if ($this->subject && !$this->isFieldExists($entity)) {
            return false;
        }

        $entityTime = $this->getEntityTime($entity);

        if (array_key_exists($this->comparison->value, $comparisonMap)) {
            return $comparisonMap[$this->comparison->value]($entityTime);
        }

        return false;
    }

    private function getComparisonMap(): array
    {
        return [
            Comparison::is->value => fn ($entityTime) => $entityTime->equalTo($this->value),
            Comparison::isNot->value => fn ($entityTime) => !$entityTime->equalTo($this->value),
            Comparison::isAfter->value => fn ($entityTime) => $entityTime->greaterThan($this->value),
            Comparison::isBefore->value => fn ($entityTime) => $entityTime->lessThan($this->value),
            Comparison::isOnOrAfter->value => fn ($entityTime) => $entityTime->greaterThan($this->value) || $entityTime->equalTo($this->value),
            Comparison::isOnOrBefore->value => fn ($entityTime) => $entityTime->lessThan($this->value) || $entityTime->equalTo($this->value),

        ];
    }
    protected function isFieldExists(array|object|null $entity): bool
    {
        if (is_array($entity) && !array_key_exists($this->subject, $entity)) {
            return false;
        }
        if (is_object($entity) && !property_exists($entity, $this->subject)) {
            return false;
        }

        return true;
    }
    /**
     * Gets the time value to compare from a Model or DateTime object.
     *
     * @param Model|Carbon $entity
     * @param string|null $field
     * @return Carbon
     */
    private function getEntityTime($entity): Carbon
    {
        dd($entity, $this->subject);
        if ($this->subject === null) {
            return Carbon::now();
        }
        return is_array($entity) ? $entity[$this->subject] : $entity->{$this->subject};
    }
}
