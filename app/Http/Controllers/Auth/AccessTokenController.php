<?php

namespace App\Http\Controllers\Auth;

use App;
use App\Events\User\UserLoggedIn;
use App\Http\Requests\Auth\SmsChallengeRequest;
use App\Models\ApiToken;
use App\Models\User;
use Cache;
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

    // /**
    //  * Overrides token issue to handle user_uuid
    //  *
    //  * @param  \Psr\Http\Message\ServerRequestInterface  $request
    //  * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
    //  */
    // public function issueToken(ServerRequestInterface $request)
    // {
    //     if (config('iam.user_field') === 'email') {
    //         return parent::issueToken($request);
    //     }

    //     $body = (array)$request->getParsedBody();
    //     try {
    //         if ($this->twilioService->verify($request->input('phone'), $request->input('code'))) {

    //             /** @var Customer */
    //             $customer = $verificationData->getParent();

    //             Cache::put("registration: $sanitizedPhoneNumber", ['parent' => $customer->id]);

    //             $loginAggregate->verificationSucceeded($verificationData)->persist();

    //             return response()->json(['message' => 'Phone number verified'], 200);
    //         }
    //     } catch (\Throwable $th) {
    //         $loginAggregate->verificationFailed(
    //             $verificationData,
    //             $th->getMessage(),
    //             $th->getCode(),
    //         )->persist();
    //         return response()->json(['error' => 'Verification code could not be verified'], 422);
    //     }
    // }

    // /**
    //  * @param VerificationRequest $request
    //  *
    //  * @return JsonResponse
    //  */
    // protected function verify(VerificationRequest $request): JsonResponse
    // {
    //     if (!$request->filled('code')) {
    //         return response()->json(['error' => 'Verification code is required'], 422);
    //     }

    //     $verificationData = CustomerVerificationData::fromRequest($request);
    //     try {
    //         $loginAggregate = LoginAggregate::retrieve($verificationData->userUuid);
    //     } catch (\Throwable $th) {
    //         info($th->getMessage(), ['exception' => $th]);
    //         return response()->json(['error' => 'Verification code could not verified'], 401);
    //     }

    //     $sanitizedPhoneNumber = $this->twilioService->sanitizePhoneNumber($request->input('phone'));

    //     try {
    //         if ($this->twilioService->verify($request->input('phone'), $request->input('code'))) {

    //             /** @var Customer */
    //             $customer = $verificationData->getParent();

    //             Cache::put("registration: $sanitizedPhoneNumber", ['parent' => $customer->id]);

    //             $loginAggregate->verificationSucceeded($verificationData)->persist();

    //             return response()->json(['message' => 'Phone number verified'], 200);
    //         }
    //     } catch (\Throwable $th) {
    //         $loginAggregate->verificationFailed(
    //             $verificationData,
    //             $th->getMessage(),
    //             $th->getCode(),
    //         )->persist();
    //         return response()->json(['error' => 'Verification code could not be verified'], 422);
    //     }
    // }
}
