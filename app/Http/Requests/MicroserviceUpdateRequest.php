<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MicroserviceUpdateRequest extends FormRequest
{
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'name'     => ['sometimes', 'required', 'string', 'max:255', 'unique:microservices,name,' . $this->route('microservice')->uuid . ',uuid'],
            'endpoint' => ['sometimes', 'required', 'url'],
            'api_key'  => ['sometimes', 'required', 'string'],
            'status'   => ['sometimes', 'required', 'string', 'in:active,inactive'],
        ];
    }
}