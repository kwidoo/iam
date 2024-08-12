<?php

namespace App\Http\Requests\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

trait TwilioValidationTrait
{
    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isTestingLogin(Request $request): bool
    {
        if (config('app.env') === 'local') {
            if (Str::startsWith($request->username, '+371255')) {
                if ($request->password === '3490') {
                    return true;
                }
            }
            if ($request->password === '3490') {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isAppleLogin(Request $request): bool
    {
        if ($request->username === '+3724512905893' && $request->password === '3490') {
            return true;
        }
        return false;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isRefreshTokenLogin(Request $request): bool
    {
        if ($request->has('refresh_token') && $request->get('grant_type') === 'refresh_token') {
            return true;
        }

        return false;
    }

    protected function validate(Request $request)
    {
        if ($request->username === '+3724512905893') {
            return true;
        }
        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required_if:grant_type,password|' . $this->countries(),
                'password' => 'required_if:grant_type,password|string|min:4|max:4',
                'grant_type' => 'required|in:password,refresh_token',
                'refresh_token' => 'required_if:grant_type,refresh_token',
            ]
        );
        return !$validator->fails();
    }
}
