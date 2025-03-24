<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionUpdateRequest extends FormRequest
{
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'name'       => ['sometimes', 'required', 'string', 'max:255', 'unique:permissions,name,' . $this->route('permission')->uuid . ',uuid'],
            'guard_name' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }
}