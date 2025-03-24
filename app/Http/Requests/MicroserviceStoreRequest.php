<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MicroserviceStoreRequest extends FormRequest
{
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'name'     => ['required', 'string', 'max:255', 'unique:microservices,name'],
            'endpoint' => ['required', 'url'],
            'api_key'  => ['required', 'string'],
            'status'   => ['required', 'string', 'in:active,inactive'],
        ];
    }
}