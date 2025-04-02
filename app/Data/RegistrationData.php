<?php

namespace App\Data;

use App\Enums\RegistrationFlow;
use App\Models\Organization;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class RegistrationData extends Data
{
    public function __construct(
        #[Required]
        #[In(['email', 'phone'])]
        public string $method,

        #[Required]
        #[BooleanType]
        public bool $otp,

        public ?string $value = null,
        public ?string $password = null,
        public ?string $fname = null,
        public ?string $lname = null,
        public ?string $dob = null,
        public ?string $gender = null,
        public ?Organization $organization = null,
        public ?string $inviteCode = null,
        public ?string $orgName = null,
        public ?User $user = null,
        public ?Profile $profile = null,
        public ?string $flow = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $payload = $request->all();

        $method = $payload['method'] ?? '';
        $value = Arr::get($payload, $method);

        return new self(
            method: $method,
            otp: filter_var($payload['otp'] ?? false, FILTER_VALIDATE_BOOLEAN),
            value: $value,
            password: $payload['password'] ?? null,
            fname: $payload['fname'] ?? null,
            lname: $payload['lname'] ?? null,
            dob: $payload['dob'] ?? null,
            gender: $payload['gender'] ?? null,
            organization: self::resolveOrganization($payload['organization'] ?? null),
            inviteCode: $payload['invite_code'] ?? null,
            orgName: $payload['org_name'] ?? null,
            flow: $payload['flow'] ?? RegistrationFlow::MAIN_ONLY->value,
        );
    }

    protected static function resolveOrganization(?string $slug): ?Organization
    {
        return $slug ? Organization::where('slug', $slug)->firstOrFail() : null;
    }

    public static function rules(): array
    {
        return [
            'password' => ['required_if:otp,false', 'string', 'min:8'],
            'password_confirmation' => ['required_if:otp,false', 'same:password'],

            'email' => ['required_if:method,email', 'email'],
            'phone' => ['required_if:method,phone', 'string', Rule::unique('contacts', 'value')],
        ];
    }
}
