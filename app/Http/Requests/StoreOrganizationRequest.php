<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
{
    public function authorize()
    {
        // Any authenticated user can create an organization.
        return true;
    }

    public function rules()
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'logo'        => ['nullable', 'string'], // Adjust if handling file uploads.
        ];
    }
}
