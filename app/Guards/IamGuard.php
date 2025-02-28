<?php

namespace App\Guards;

use App\Models\ApiToken;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class IamGuard
 *
 * This class implements the Guard interface and provides authentication functionality
 * using a custom IAM token.
 */
class IamGuard implements Guard
{
    /**
     * @var Authenticatable
     */
    protected ?Authenticatable $user;

    /**
     * Create a new IamGuard instance.
     *
     * @param UserProvider $provider The user provider instance.
     * @param Request $request The request instance.
     */
    public function __construct(protected UserProvider $provider, protected Request $request)
    {
        //
    }

    /**
     * Check if a user is authenticated.
     *
     * @return bool True if the user is authenticated, false otherwise.
     */
    public function check()
    {
        return !is_null($this->user());
    }

    /**
     * Check if a user is a guest.
     *
     * @return bool True if the user is a guest, false otherwise.
     */
    public function guest()
    {
        return !$this->check();
    }

    /**
     * Get the authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null The authenticated user instance, or null if not authenticated.
     */
    public function user()
    {
        if (!isset($this->user->id)) {
            return $this->user;
        }

        $token = $this->request->header('X-IAM-Token');
        if ($token) {
            $userId = $this->validateTokenAndGetUserId($token);
            if ($userId) {
                $this->user = $this->provider->retrieveById($userId);
            }
        }

        return $this->user;
    }

    /**
     * Validate the IAM token and get the user ID.
     *
     * @param string $token The IAM token to validate.
     * @return string|null The user ID if the token is valid, null otherwise.
     */
    protected function validateTokenAndGetUserId($token)
    {
        return ApiToken::find($token)?->user_uuid;
    }

    /**
     * Get the ID for the authenticated user.
     *
     * @return mixed The ID for the authenticated user, or null if not authenticated.
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function id()
    {
        $user = $this->user();
        if ($user) {
            return $user->getAuthIdentifier();
        }
    }

    /**
     * Validate the user credentials.
     *
     * @param array<string,string> $credentials The user credentials.
     * @return bool True if the credentials are valid, false otherwise.
     */
    public function validate(array $credentials = [])
    {
        // Implement logic to validate credentials
        return true;
    }

    /**
     * Set the authenticated user.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user The user instance to set as authenticated.
     * @return $this
     */
    public function setUser(\Illuminate\Contracts\Auth\Authenticatable $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Check if the guard has an authenticated user.
     *
     * @return bool True if the guard has an authenticated user, false otherwise.
     */
    public function hasUser()
    {
        return $this->user !== null;
    }
}
