<?php

namespace App\Http\Requests;

use App\Rules\PrimaryPhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class StoreUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge([
            'type' => ['required', 'string'],
            'name' => ['required', 'string'],
            // 'user_uuid' => ['required', 'string'],
            'reference_id' => ['required', 'string'],

        ], $this->rulesForRegisterUsing());
    }

    /**
     * Set rules for registration using email or phone.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected function rulesForRegisterUsing()
    {
        $loginUsing = $this->input('type', 'email');
        $withPassword = config('iam.use_password', true) ? [
            'password' => ['required', 'string']
        ] : [];
        if (config('with_password_confirmation', true)) {
            $withPassword['password_confirmation'] = ['required', 'string', 'same:password'];
        }

        switch ($loginUsing) {
            case 'phone':
                return [
                    'full_phone' => ['required', 'string'],
                    'country_code' => ['required', 'string'],
                    'phone' => ['required', 'string'],
                    'is_primary' => [new PrimaryPhoneRule($this->full_phone)],
                ];
            case 'email':
            default:
                return [
                    'email' => [
                        'required',
                        'email',
                        Rule::unique('emails', 'email')->where(function ($query) {
                            return $query->whereNull('deleted_at');
                        }),
                    ],
                    ...$withPassword
                ];
        }
    }

    /**
     * @return void
     */
    public function prepareForValidation(): void
    {
        $loginUsing = $this->input('type', 'email');
        if ($loginUsing === 'phone') {
            $this->merge([
                'full_phone' => $this->input('country_code') . $this->input('phone'),
            ]);
        }

        $this->merge([
            'type' => $this->input('type', 'email'),
            'reference_id' => Str::uuid()->toString(),
        ]);
    }
}
