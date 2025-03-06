<?php

namespace App\Providers;

use App\Services\IamUserResolver;
use Illuminate\Support\ServiceProvider;
use Kwidoo\MultiAuth\Contracts\UserResolver;
use Kwidoo\SmsVerification\Contracts\VerifierInterface;
use Kwidoo\SmsVerification\VerifierFactory;
use Laravel\Passport\Bridge\UserRepository as PassportUserRepository;
use App\Services\UserRepository;


class UserProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // bind sms verification to Kwidoo\SmsVerification package. Make param accept SMS provider
        $this->app->bind(VerifierInterface::class, function ($app) {
            return (new VerifierFactory($app))->make();
        });

        // bind user resolver to this User/Contact models. In case of regular models bind to GeneralUserResolver from Kwidoo\MultiAuth package
        $this->app->bind(UserResolver::class, IamUserResolver::class);


        $this->app->bind(PassportUserRepository::class, UserRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
