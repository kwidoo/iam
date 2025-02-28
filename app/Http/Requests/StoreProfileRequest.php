<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<int|string, array<int, \Illuminate\Validation\Rules\Unique|string>|string>
     */
    public function rules(): array
    {
        $userUuid = $this->user()?->uuid;
        return [
            'name' => 'required', 'string',
            'type' => [
                'required',
                'string',
                'max:255',
                Rule::unique('profiles', 'type')->where(function ($query) use ($userUuid) {
                    return $query->where('user_uuid', $userUuid)
                        ->whereNull('deleted_at');
                })
            ],
        ];
    }
}
