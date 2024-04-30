<?php

namespace App\Providers;

use App\Aggregates\UserAggregate;
use App\Contracts\AddEmailService;
use App\Contracts\CreateEmail;
use App\Contracts\CreateUser;
use App\Contracts\CreateUserService;
use App\Contracts\LoginUser;
use App\Contracts\RemoveEmailService;
use App\Contracts\SendEmailVerificationService;
use App\Contracts\SetPrimaryEmailService;
use App\Contracts\UserAggregate as ContractsUserAggregate;
use App\Contracts\VerifyEmailService;
use App\Services\AddEmailService as ServicesAddEmailService;
use App\Services\CreateRootUserService;
use App\Services\RemoveEmailService as ServicesRemoveEmailService;
use App\Services\SendEmailVerificationService as ServicesSendEmailVerificationService;
use App\Services\SetPrimaryEmailService as ServicesSetPrimaryEmailService;
use App\Services\VerifyEmailService as ServicesVerifyEmailService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Passport::enablePasswordGrant();

        $this->app->bind(
            CreateUserService::class,
            CreateRootUserService::class
        );

        $this->app->bind(
            AddEmailService::class,
            ServicesAddEmailService::class
        );

        $this->app->bind(ContractsUserAggregate::class, UserAggregate::class);
        $this->app->bind(CreateUser::class, UserAggregate::class);
        $this->app->bind(CreateEmail::class, UserAggregate::class);
        $this->app->bind(LoginUser::class, UserAggregate::class);

        VerifyEmail::$createUrlCallback = function ($notifiable) {
            URL::forceRootUrl('http://rentapp.home');
            $url = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(60),
                ['id' => $notifiable->getKey()],
                false,
            );
            Log::info("Generated URL: {$url}");
            URL::forceRootUrl(config('app.url'));

            return config('app.url') . $url;
        };

        $this->app->bind(VerifyEmailService::class, ServicesVerifyEmailService::class);
        $this->app->bind(SendEmailVerificationService::class, ServicesSendEmailVerificationService::class);
        $this->app->bind(SetPrimaryEmailService::class, ServicesSetPrimaryEmailService::class);
        $this->app->bind(RemoveEmailService::class, ServicesRemoveEmailService::class);
    }
}
