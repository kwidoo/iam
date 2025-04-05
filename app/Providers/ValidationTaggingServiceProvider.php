<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kwidoo\Mere\Validators\{
    CanInviteValidator,
    ConflictValidator,
    FlowValidator,
    InviteValidator,
    ReuseValidator,
};

class ValidationTaggingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->tag([
            FlowValidator::class,
            ReuseValidator::class,
            ConflictValidator::class,
        ], 'registration.validators');

        $this->app->tag([
            InviteValidator::class,
        ], 'invite.accept.validators');

        $this->app->tag([
            CanInviteValidator::class,
        ], 'invite.send.validators');
    }
}
