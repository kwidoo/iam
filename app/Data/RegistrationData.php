<?php

namespace App\Data;

use App\Models\Organization;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class RegistrationData extends Data
{
    public function __construct(
        #[In(['email', 'phone'])]
        #[Required()]
        public string $method,

        #[Required()]
        #[BooleanType]
        public bool $otp,

        public ?string $value,
        public ?string $password = null,
        public ?string $fname = null,
        public ?string $lname = null,
        public ?string $dob = null,
        public ?string $gender = null,
        public ?Organization $organization = null,
        public ?User $user = null,
        public ?Profile $profile = null,
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

    public static function rules(): array
    {
        return [
            'password' => ['required_if:otp,false', 'string', 'min:8'],
            'password_confirmation' => ['required_if:otp,false', 'string', 'same:password'],

            'email' => ['required_if:method,email', 'email'],
            'phone' => ['required_if:method,phone', 'string'],
        ];
    }
}
