<?php

namespace App\Providers;

use App\Aggregates\UserAggregate;
use App\Contracts\AddEmailService as AddEmailServiceContract;
use App\Contracts\CreateEmail;
use App\Contracts\RemoveEmailService as RemoveEmailServiceContract;
use App\Contracts\SendEmailVerificationService as SendEmailVerificationServiceContract;
use App\Contracts\SetPrimaryEmailService as SetPrimaryEmailServiceContract;
use App\Contracts\VerifyEmailService as VerifyEmailServiceContract;
use App\Services\AddEmailService;
use App\Services\RemoveEmailService;
use App\Services\SendEmailVerificationService;
use App\Services\SetPrimaryEmailService;
use App\Services\VerifyEmailService;
use Illuminate\Support\ServiceProvider;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class EmailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(
            AddEmailServiceContract::class,
            AddEmailService::class
        );

        $this->app->bind(CreateEmail::class, UserAggregate::class);

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

        $this->app->bind(VerifyEmailServiceContract::class, VerifyEmailService::class);
        $this->app->bind(SendEmailVerificationServiceContract::class, SendEmailVerificationService::class);
        $this->app->bind(SetPrimaryEmailServiceContract::class, SetPrimaryEmailService::class);
        $this->app->bind(RemoveEmailServiceContract::class, RemoveEmailService::class);
    }
}
