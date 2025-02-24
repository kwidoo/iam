<?php

namespace App\Data\Update;

use App\Contracts\Models\UserWriteModel;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class UserData extends Data
{
    /**
     * @var \App\Model\User
     */
    public $user;

    public function __construct(
        #[MapInputName('user_uuid')]
        public string $uuid,
        public string $name,
        public string $email,
        #[MapInputName('email_verified_at')]
        public ?string $emailVerifiedAt,
        public string $password,
        #[MapInputName('reference_id')]
        public string $referenceId,
        //
    ) {
        $this->user = app()->make(UserWriteModel::class)->find($uuid);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'user_uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->emailVerifiedAt,
            'password' => $this->password,
            'reference_id' => $this->referenceId,
        ];
    }

    /**
     * @param mixed ...$payloads
     *
     * @return static
     */
    public static function from(mixed ...$payloads): static
    {
        $payload = $payloads[0];
        return new self(
            $payload['user_uuid'],
            $payload['name'],
            $payload['email'],
            $payload['email_verified_at'],
            $payload['password'],
            $payload['reference_id'],
        );
    }
}
