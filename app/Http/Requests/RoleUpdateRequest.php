<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleUpdateRequest extends FormRequest
{
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'name'       => ['sometimes', 'required', 'string', 'max:255', 'unique:roles,name,' . $this->route('role')->uuid . ',uuid'],
            'guard_name' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }
}