<?php

namespace App\Providers;

use App\Contracts\Services\PasswordResetService as PasswordResetServiceContract;
use App\Services\IamUserResolver;
use App\Services\PasswordResetService;
use Illuminate\Support\ServiceProvider;
use Kwidoo\MultiAuth\Contracts\UserResolver;
use Kwidoo\SmsVerification\Contracts\VerifierInterface;
use Kwidoo\SmsVerification\VerifierFactory;
use Laravel\Passport\Bridge\UserRepository as PassportUserRepository;
use App\Services\UserRepository;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;
use Kwidoo\Contacts\Contracts\Contact;
use Kwidoo\Contacts\Contracts\TokenGenerator;
use Laravel\Passport\Passport;

class IamProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        Passport::enablePasswordGrant();

        // bind sms verification to Kwidoo\SmsVerification package. Make param accept SMS provider
        $this->app->bind(VerifierInterface::class, function ($app) {
            return (new VerifierFactory($app))->make();
        });

        // bind user resolver to this User/Contact models. In case of regular models bind to GeneralUserResolver from Kwidoo\MultiAuth package
        $this->app->bind(UserResolver::class, IamUserResolver::class);

        $this->app->bind(PassportUserRepository::class, UserRepository::class);

        // bind password reset service to this PasswordResetService. Make param accept Contact
        $this->app->bind(PasswordResetServiceContract::class, function ($app, $params) {
            $contact = $params['contact'] ?? null;
            if (!$contact instanceof Contact) {
                throw new InvalidArgumentException('A valid Contact instance is required.');
            }

            $available = config('contacts.verifiers', []);
            if (!array_key_exists($contact->type, $available)) {
                throw new InvalidArgumentException("Unsupported contact type: {$contact->type}");
            }

            $verifier = $app->make($available[$contact->type], [
                'tokenGenerator' => $app->make(TokenGenerator::class, [
                    'contact' => $contact
                ]),
                'contact' => $contact
                // to add password reset template here
            ]);

            return new PasswordResetService($verifier, $contact);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('SuperAdmin') ? true : null;
        });
    }
}
