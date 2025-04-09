<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for user registration.
 * Handles validation of registration data and prepares it for processing.
 *
 * @category App\Http\Requests
 * @package  App\Http\Requests
 * @author   John Doe <john.doe@example.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/your-repo
 */
class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Registration is always allowed as it's a public endpoint.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * Rules are dynamically determined based on registration method and OTP usage.
     *
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        $rules = [
            'name'   => ['required', 'string', 'max:255'],
            'method' => ['required', Rule::in(['email', 'phone'])],
            'otp'    => ['required', 'boolean'],
        ];

        $method = $this->input('method');
        if (method_exists($this, 'rulesFor' . ucfirst($method)) && config('iam.allow_' . $method)) {
            $rules = array_merge($rules, $this->{'rulesFor' . ucfirst($method)}());
        }

        if (!$this->input('otp')) {
            $rules = array_merge($rules, $this->withoutOTP());
        }

        return $rules;
    }

    /**
     * Get validation rules specific to email registration.
     *
     * @return array<string, array<string>>
     */
    protected function rulesForEmail(): array
    {
        return [
            'email'    => ['required', 'email'],
        ];
    }

    /**
     * Get validation rules specific to phone registration.
     *
     * @return array<string, array<string>>
     */
    protected function rulesForPhone(): array
    {
        return [
            'phone'    => ['required', 'string'],
        ];
    }

    /**
     * Get validation rules for password-based registration (non-OTP).
     *
     * @return array<string, array<string>>
     */
    protected function withoutOTP(): array
    {
        return [
            'password' => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'string', 'same:password'],
        ];
    }

    /**
     * Prepare the data for validation.
     * Converts string 'true'/'false' to boolean for OTP field.
     *
     * @return void
     */
    public function prepareForValidation()
    {
        $this->merge(
            [
            'otp' => $this->input('otp') === 'true',
            ]
        );
    }
}
