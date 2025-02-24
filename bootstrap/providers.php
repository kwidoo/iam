<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\EmailServiceProvider::class,
    App\Providers\EventSourcingServiceProvider::class,
    App\Providers\UserAggregateProvider::class,
    App\Providers\UserProviders::class,
    MongoDB\Laravel\MongoDBServiceProvider::class,
];
