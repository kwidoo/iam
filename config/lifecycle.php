<?php

return [
    'strategies' => [
        'logging' => \Kwidoo\Lifecycle\Strategies\Logging\DefaultLogStrategy::class,
        'events' => Kwidoo\Lifecycle\Strategies\Events\DefaultEventStrategy::class,
        'retry' => Kwidoo\Lifecycle\Strategies\Retry\DefaultRetryStrategy::class,
        'transactions' => Kwidoo\Lifecycle\Strategies\Transactions\DefaultTransactionStrategy::class,
    ],
];
