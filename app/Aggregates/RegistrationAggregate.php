<?php

namespace App\Aggregates;

use Kwidoo\Mere\Contracts\Aggregate;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class RegistrationAggregate extends AggregateRoot implements Aggregate {}
