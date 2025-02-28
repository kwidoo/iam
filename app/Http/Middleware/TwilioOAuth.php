<?php

namespace App\Http\Middleware;

use App\Http\Requests\Traits\TwilioValidationTrait;
use Closure;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class TwilioOAuth
{
    use TwilioValidationTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->path() !== 'oauth/token') {
            return $next($request);
        }
        if ($this->validate($request) === false) {
            return response()->json('Mobile number or Auth code is not valid3', 403);
        }
        if ($this->isRefreshTokenLogin($request) || $this->isAppleLogin($request) || $this->isTestingLogin($request)) {
            return $next($request);
        }
        $twilio = app()->make(Client::class, [
            'username' => config('twilio.sid'),
            'password' => config('twilio.auth_token')
        ]);

        $verification = $twilio
            ->verify
            ->v2
            ->services(config('twilio.verify_sid'))
            ->verificationChecks
            ->create(
                $request->input('password'),
                [
                    'to' => str_replace(" ", "", $request->input('username'))
                ]
            );
        if (!$verification->valid) {
            return response()->json('Mobile number or Auth code is not valid4', 403);
        }

        return $next($request);
    }
}
