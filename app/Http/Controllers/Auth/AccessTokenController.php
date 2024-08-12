<?php

namespace App\Http\Controllers\Auth;

use App;
use App\Events\User\UserLoggedIn;
use App\Http\Requests\Auth\SmsChallengeRequest;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Http\Controllers\AccessTokenController as BaseAccessTokenController;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\AuthorizationServer;
use Illuminate\Support\Str;
use Laravel\Passport\Exceptions\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;
use Twilio\Rest\Client;
use Twilio\Rest\Verify\V2\Service\VerificationCheckInstance;

class AccessTokenController extends BaseAccessTokenController
{
    /**
     * @var string
     */
    protected string $referenceUuid;

    /**
     * @param AuthorizationServer $server
     * @param TokenRepository $tokens
     */
    public function __construct(AuthorizationServer $server, TokenRepository $tokens)
    {
        $this->referenceUuid = Str::uuid()->toString();
        parent::__construct($server, $tokens);
    }

    /**
     * Handle default token issue process
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Laravel\Passport\Exceptions\OAuthServerException
     */
    protected function handleDefaultTokenIssue(ServerRequestInterface $request)
    {
        try {
            $response = parent::issueToken($request);
            return response()->json(json_decode((string)$response->getContent())); // @todo should not decode/encode
        } catch (OAuthServerException $e) {
            throw $e;
        }
    }

    /**
     * Prepare the response after issuing token
     *
     * @param \Illuminate\Http\Response $response
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    protected function prepareResponse($response, User $user)
    {
        $data = json_decode((string) $response->getContent(), true);
        $data['email_verified'] = $user->hasVerifiedEmail();
        $data['user_uuid'] = $user->uuid;
        $data['reference_id'] = $this->referenceUuid;
        $token = ApiToken::create(['user_uuid' => $user->uuid]);
        $data['iam_token'] = $token->uuid;

        event(new UserLoggedIn($user, $data));

        $this->updateUserPassword($user,  bcrypt(bin2hex(random_bytes(16))));

        return response()->json($data, 200);
    }

    /**
     * @param SmsChallengeRequest $request
     *
     * @return JsonResponse
     */
    public function getPhoneChallenge(SmsChallengeRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['reference_id'] = Str::uuid()->toString();

        if ($this->twilioAuthentication($request)) {

            cache()->put('phone_validation_' . $validated['full_phone'], $validated, 3600);

            return response()->json([
                'status' => 'ok',
                'reference' => $validated['reference_id'],
            ]);
        };

        throw new AuthenticationException('Twilio authentication failed');
    }

    /**
     * @param SmsChallengeRequest $request
     *
     * @return VerificationCheckInstance|bool
     */
    protected function twilioAuthentication(SmsChallengeRequest $request)
    {
        try {
            // Get credentials from .env
            $twilio = App::make(Client::class, [
                'username' => config('twilio.sid'),
                'password' => config('twilio.auth_token')
            ]);

            /** @var VerificationCheckInstance */
            $verification = $twilio
                ->verify
                ->v2
                ->services(config('twilio.verify_sid'))
                ->verifications
                ->create(
                    str_replace(" ", "", $request->input('full_phone')),
                    'sms'
                );

            return $verification;
        } catch (\Exception $e) {
            Log::error('Twilio authentication failed: ' . $e->getMessage());
            return false;
        }
    }
}
