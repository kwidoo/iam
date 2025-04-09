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
use Illuminate\Support\Str;

/**
 * Data Transfer Object for user registration.
 * Contains all necessary information for user registration process.
 *
 * @category App\Data
 * @package  App\Data
 * @author   John Doe <john.doe@example.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/your-repo
 */
class RegistrationData extends Data
{
    public ?string $id = null;
    /**
     * Initialize registration data with all possible fields.
     *
     * @param string            $method       Registration method (email or phone)
     * @param bool              $otp          Whether to use OTP verification
     * @param string|null       $value        The email or phone value
     * @param string|null       $password     User password (if not using OTP)
     * @param string|null       $fname        User's first name
     * @param string|null       $lname        User's last name
     * @param string|null       $dob          User's date of birth
     * @param string|null       $gender       User's gender
     * @param Organization|null $organization Organization to associate with
     * @param string|null       $inviteCode   Organization invite code
     * @param string|null       $orgName      Organization name for new org creation
     * @param User|null         $user         User model (if already created)
     * @param Profile|null      $profile      User profile (if already created)
     * @param string|null       $flow         Registration flow type
     */
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
        ?string $id = null,
    ) {
        $this->id = $id ?? Str::uuid()->toString();
    }

    /**
     * Create RegistrationData instance from HTTP request.
     * Handles data transformation and validation.
     *
     * @param Request $request
     *
     * @return RegistrationData
     */
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

    /**
     * Resolve organization from slug.
     *
     * @param string|null $slug
     *
     * @return Organization|null
     */
    protected static function resolveOrganization(?string $slug): ?Organization
    {
        return $slug ? Organization::where('slug', $slug)->firstOrFail() : null;
    }

    /**
     * Get validation rules for registration data.
     *
     * @return array<string, array<string>>
     */
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
