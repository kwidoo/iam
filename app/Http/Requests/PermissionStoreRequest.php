<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionStoreRequest extends FormRequest
{
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'name'       => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'guard_name' => ['required', 'string', 'max:255'],
        ];
    }
}