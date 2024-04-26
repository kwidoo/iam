<?php

namespace App\Http\Controllers;

use App\Events\User\UserLoginFailed;
use App\Models\User;
use App\Services\LoginService;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Passport\Exceptions\OAuthServerException;
use Laravel\Passport\Http\Controllers\AccessTokenController as ControllersAccessTokenController;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Support\Str;

class AccessTokenController extends ControllersAccessTokenController
{
    /**
     * Overrides token issue to handle user_uuid
     *
     * @param  \Psr\Http\Message\ServerRequestInterface  $request
     * @return \Illuminate\Http\Response
     */
    public function issueToken(ServerRequestInterface $request)
    {
        $uuid =  Str::uuid()->toString();

        $parsedBody = $request->getParsedBody();

        if (!isset($parsedBody['login'])) {
            try {
                $response = parent::issueToken($request);

                return $response;
            } catch (OAuthServerException $e) {
                throw $e;
            }
        }

        try {
            $user = User::whereHas('email', fn (Builder $q) => $q->where('email', $parsedBody['login']))->firstOrFail();
        } catch (\Exception $e) {
            event(new UserLoginFailed(null, ['reference' => $uuid]));
            throw new $e;
        }

        $newParsedBody = array_merge($parsedBody, ['username' => $user->uuid]);
        unset($newParsedBody['login']);

        $newRequest = $request->withParsedBody($newParsedBody);

        try {
            $response = parent::issueToken($newRequest);
        } catch (OAuthServerException $e) {
            event(new UserLoginFailed($user, ['reference' => $uuid]));
            throw $e;
        }

        $data = json_decode($response->getContent(), true);
        $data['email_verified'] = $user->hasVerifiedEmail();
        $data['user_uuid'] = $user->uuid;
        $data['reference_id'] = $uuid;

        LoginService::login($user, $data);

        return response()->json($data, 200);
    }
}
