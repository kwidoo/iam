<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule','array<mixed>','string>
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
     * @return array
     */
    protected function rulesForEmail(): array
    {
        return [
            'email'    => ['required', 'email'],
        ];
    }

    /**
     * @return array
     */
    protected function rulesForPhone(): array
    {
        return [
            'phone'    => ['required', 'string'],

        ];
    }

    /**
     * @return array
     */
    protected function withoutOTP(): array
    {
        return [
            'password' => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'string', 'same:password'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'otp' => $this->input('otp') === 'true',
        ]);
    }
}
