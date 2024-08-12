<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SmsChallengeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
        return [
            'phone' => 'required|string',
            'country_code' => 'required|string',
            'full_phone' => ['required', 'phone:EE,LV,FR,mobile',], // @todo make configurable
        ];
    }

    /**
     * @return void
     */
    public function prepareForValidation(): void
    {
        $this->merge([
            'full_phone' => $this->input('country_code') . $this->input('phone'),
        ]);
    }
}
