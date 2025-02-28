<?php

namespace App\Conditions;

use App\Enums\Comparison;

class EntityBasedCondition
{
    /**
     * EntityBasedCondition constructor.
     *
     * @param Comparison $comparison Comparison operator to use
     * @param string $subject The subject field of the entity to evaluate
     * @param string|int|bool|null|array $value The value to compare against
     */
    public function __construct(
        public Comparison $comparison,
        public string $subject,
        public string|int|bool|null|array $value = null
    ) {
    }

    /**
     * Evaluate the condition against a given entity.
     *
     * @param array|object $entity Entity to evaluate
     * @return bool True if the condition matches, false otherwise
     */
    public function evaluate(array|object $entity): bool
    {
        if (!$this->isFieldExists($entity)) {
            return false;
        }

        $entityValue = $this->getEntityValue($entity);

        $comparisonMap = $this->getComparisonMap();

        if (array_key_exists($this->comparison?->value, $comparisonMap)) {
            return $comparisonMap[$this->comparison->value]($entityValue);
        }

        return false;
    }

    protected function getComparisonMap(): array
    {
        return [
            Comparison::is->value => fn ($value) => $this->compareEqual($value),
            Comparison::isNot->value => fn ($value) => !$this->compareEqual($value),
            Comparison::contains->value => fn ($value) => $this->compareContains($value),
            Comparison::doesNotContain->value => fn ($value) => !$this->compareContains($value),
            Comparison::isGreaterThan->value => fn ($value) => $this->compareGreaterThan($value),
            Comparison::isLessThan->value => fn ($value) => $this->compareLessThan($value),
            Comparison::isGreaterThanOrEqualTo->value => fn ($value) => $this->compareGreaterThan($value) || $this->compareEqual($value),
            Comparison::isLessThanOrEqualTo->value => fn ($value) => $this->compareLessThan($value) || $this->compareEqual($value),
            Comparison::isInstanceOf->value => fn ($value) => $value instanceof $this->value,
            Comparison::isEmpty->value => fn ($value) => empty($value),
            Comparison::isNotEmpty->value => fn ($value) => !empty($value),
            Comparison::isNull->value => fn ($value) => is_null($value),
            Comparison::isNotNull->value => fn ($value) => !is_null($value)
        ];
    }


    protected function getEntityValue(array|object $entity): mixed
    {
        return is_array($entity) ? $entity[$this->subject] : $entity->{$this->subject};
    }

    protected function isFieldExists(array|object $entity): bool
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
     * Compare if the entity value is equal to the condition value.
     *
     * @param mixed $entityValue
     * @return bool
     */
    protected function compareEqual(mixed $entityValue): bool
    {
        return $entityValue === $this->value;
    }

    /**
     * Compare if the entity value contains the condition value.
     *
     * @param mixed $entityValue
     * @return bool
     */
    protected function compareContains(mixed $entityValue): bool
    {
        if (is_array($entityValue)) {
            return in_array($this->value, $entityValue);
        }
        return str_contains($entityValue, $this->value);
    }

    /**
     * Compare if the entity value is greater than the condition value.
     *
     * @param mixed $entityValue
     * @return bool
     */
    protected function compareGreaterThan(mixed $entityValue): bool
    {
        if (is_string($this->value)) {
            return strlen($entityValue) > strlen($this->value);
        }
        if (is_array($this->value) && is_array($entityValue)) {
            return count($entityValue) > count($this->value);
        }
        return $entityValue > $this->value;
    }

    /**
     * Compare if the entity value is less than the condition value.
     *
     * @param mixed $entityValue
     * @return bool
     */
    protected function compareLessThan(mixed $entityValue): bool
    {
        if (is_string($this->value)) {
            return strlen($entityValue) < strlen($this->value);
        }
        if (is_array($this->value) && is_array($entityValue)) {
            return count($entityValue) < count($this->value);
        }
        return $entityValue < $this->value;
    }
}
