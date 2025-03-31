<?php

namespace App\Data;

use App\Models\Organization;
use Illuminate\Http\Request;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class RegistrationData extends Data
{
    public function __construct(
        #[In(['email', 'phone'])]
        #[Required(message: 'The method must be either email or phone')]
        public string $method,
        public bool $otp,
        #[Required(message: 'The value is required')]

        public string $value,
        public ?string $password = null,
        public ?string $fname = null,
        public ?string $lname = null,
        public ?string $dob = null,
        public ?string $gender = null,
        public ?Organization $organization = null,
        public ?string $userId = null
    ) {}

    public static function fromRequest(Request $request): self
    {
        $payload = $request->all();
        $method = $payload['method'] ?? null;
        $payload['value'] = $payload[$method] ?? null;

        $organization = null;
        if (!empty($payload['organization'])) {
            $organization = Organization::where('slug', $payload['organization'])->firstOrFail();
        }

        return new self(
            method: $payload['method'] ?? '',
            otp: $payload['otp'] ?? false,
            value: $payload['value'] ?? '',
            password: $payload['password'] ?? null,
            fname: $payload['fname'] ?? null,
            lname: $payload['lname'] ?? null,
            dob: $payload['dob'] ?? null,
            gender: $payload['gender'] ?? null,
            organization: $organization,
        );
    }
}
