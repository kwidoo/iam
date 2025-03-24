<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvitationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'contact_type'  => ['required', 'in:email,phone,code'],
            'contact_value' => ['required', 'string', 'max:255'],
            'expires_at'    => ['nullable', 'date'],
        ];
    }
}
