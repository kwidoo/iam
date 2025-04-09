<?php

namespace App\Data;

use App\Enums\RegistrationIdentity;
use App\Enums\RegistrationMode;
use App\Enums\RegistrationProfile;
use App\Enums\RegistrationSecret;
use App\Enums\RegistrationFlow;
use InvalidArgumentException;
use Spatie\LaravelData\Data;

/**
 * Data Transfer Object for registration configuration.
 * Contains settings and strategies for the registration process.
 *
 * @category App\Data
 * @package  App\Data
 * @author   John Doe <john.doe@example.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/your-repo
 */
class RegistrationConfigData extends Data
{
    /**
     * Initialize registration configuration with all required settings.
     *
     * @param RegistrationIdentity $identity Identity verification method
     * @param RegistrationMode     $mode     Registration mode (open/invite-only)
     * @param RegistrationProfile  $profile  Profile creation strategy
     * @param RegistrationSecret   $secret   Secret type (password/OTP)
     * @param RegistrationFlow     $flow     Registration flow type
     */
    public function __construct(
        public RegistrationIdentity $identity,
        public RegistrationMode $mode,
        public RegistrationProfile $profile,
        public RegistrationSecret $secret,
        public RegistrationFlow $flow,
    ) {
    }

    /**
     * Get all configuration keys and their values.
     *
     * @return array<string, mixed>
     */
    public function allKeys(): array
    {
        return [
            'mode' => $this->mode,
            'identity' => $this->identity,
            'flow' => $this->flow,
            'profile' => $this->profile,
            'secret' => $this->secret,
        ];
    }

    /**
     * Create a new instance from the provided registration data.
     * Determines appropriate settings based on the registration method and flow.
     *
     * @param RegistrationData $data Registration data
     *
     * @return RegistrationConfigData
     *
     * @throws InvalidArgumentException When invalid method or flow is provided
     */
    public static function fromData(RegistrationData $data): self
    {
        return new self(
            identity: match ($data->method) {
                'email' => RegistrationIdentity::EMAIL,
                'phone' => RegistrationIdentity::PHONE,
                default => throw new InvalidArgumentException("Unknown identity method: {$data->method}"),
            },
            flow: match ($data->flow) {
                'main_only' => RegistrationFlow::MAIN_ONLY,
                'user_joins_user_org' => RegistrationFlow::USER_JOINS_USER_ORG,
                'user_creates_org' => RegistrationFlow::USER_CREATES_ORG,
                'initial_bootstrap' => RegistrationFlow::INITIAL_BOOTSTRAP,
                default => throw new InvalidArgumentException("Unknown flow: {$data->flow}"),
            },
            profile: RegistrationProfile::DEFAULT_PROFILE,
            secret: $data->otp ? RegistrationSecret::OTP : RegistrationSecret::PASSWORD,
            mode: RegistrationMode::OPEN,
        );
    }
}
