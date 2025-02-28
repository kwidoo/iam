<?php

namespace App\Providers;

use App\Projectors\EmailProjector;
use App\Projectors\MicroServiceProjector;
use App\Projectors\OrganizationProjector;
use App\Projectors\PhoneProjector;
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
            OrganizationProjector::class,
            MicroServiceProjector::class,
            PhoneProjector::class,
        ]);

        Projectionist::addReactors([
            EmailReactor::class,
        ]);
    }
}
