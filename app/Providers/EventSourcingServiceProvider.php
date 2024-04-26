<?php

namespace App\Providers;

use App\Projectors\EmailProjector;
use App\Projectors\UserProjector;
use App\Reactors\EmailReactor;
use Illuminate\Support\ServiceProvider;
use Spatie\EventSourcing\Facades\Projectionist;

class EventSourcingServiceProvider extends ServiceProvider
{
    public function register()
    {
        Projectionist::addProjectors([
            UserProjector::class,
            EmailProjector::class,
        ]);

        Projectionist::addReactors([
            EmailReactor::class,
        ]);
    }
}
