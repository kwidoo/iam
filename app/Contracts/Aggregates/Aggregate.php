<?php

namespace App\Contracts\Aggregates;

use Spatie\EventSourcing\AggregateRoots\FakeAggregateRoot;

interface Aggregate
{
    public static function retrieve(string $uuid): static;
    public function persist(): static;
    public static function fake(?string $uuid = null): FakeAggregateRoot;
}
