<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'name' => ['required', 'string'],

        ], $this->rulesForRegisterUsing());
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected function rulesForRegisterUsing()
    {
        $loginUsing = config('iam.user_field', 'email');
        switch ($loginUsing) {
            case 'phone':
                return [
                    'full_phone' => ['required', 'string', 'unique:phones,full_phone'],
                    'country_code' => ['required', 'string'],
                    'phone' => ['required', 'string'],
                ];
            case 'email':
            default:
                return [
                    'email' => [
                        'required', 'email',
                        Rule::unique('emails', 'email')->where(function ($query) {
                            return $query->whereNull('deleted_at');
                        }),
                    ],
                    'password' => ['required', 'string'],
                ];
        }
    }

    /**
     * @return void
     */
    public function prepareForValidation(): void
    {
        $loginUsing = config('iam.user_field', 'phone');
        if ($loginUsing === 'phone') {
            $this->merge([
                'full_phone' => $this->input('country_code') . $this->input('phone'),
            ]);
        }
    }
}
