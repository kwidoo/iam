<?php

namespace App\Data;

use App\Enums\RegistrationIdentity;
use App\Enums\RegistrationMode;
use App\Enums\RegistrationProfile;
use App\Enums\RegistrationSecret;
use App\Enums\RegistrationFlow;
use InvalidArgumentException;
use Spatie\LaravelData\Data;

class RegistrationConfigData extends Data
{
    public function __construct(
        public RegistrationIdentity $identity,
        public RegistrationMode $mode,
        public RegistrationProfile $profile,
        public RegistrationSecret $secret,
        public RegistrationFlow $flow,
    ) {}

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
     * Create a new instance from the provided data.
     * @param RegistrationData $data
     * @return RegistrationConfigData
     * @throws InvalidArgumentException
     */
    public static function fromData(Data $data): self
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
