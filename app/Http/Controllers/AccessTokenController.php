<?php

namespace App\Http\Controllers;

use App\Events\UserLoggedIn;
use App\Events\UserLoginFailed;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Passport\Exceptions\OAuthServerException;
use Laravel\Passport\Http\Controllers\AccessTokenController as ControllersAccessTokenController;
use Psr\Http\Message\ServerRequestInterface;

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
        $parsedBody = $request->getParsedBody();

        if (!isset($parsedBody['login'])) {
            try {
                $response = parent::issueToken($request);

                return $response;
            } catch (OAuthServerException $e) {
                throw $e;
            }
        }
        $user = User::whereHas('email', fn (Builder $q) => $q->where('email', $parsedBody['login']))->firstOrFail();

        $newParsedBody = array_merge($parsedBody, ['username' => $user->uuid]);
        unset($newParsedBody['login']);

        $newRequest = $request->withParsedBody($newParsedBody);

        try {
            $response = parent::issueToken($newRequest);
        } catch (OAuthServerException $e) {
            event(new UserLoginFailed($user));
            throw $e;
        }

        event(new UserLoggedIn($user));

        $data = json_decode($response->getContent(), true);
        $data['email_validated'] = $user->hasVerifiedEmail();
        $data['user_uuid'] = $user->uuid;

        $response = $response->setContent($data);

        return $response;
    }
}
