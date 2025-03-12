<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $email = $this->contacts()->where('is_primary', true)->where('type', 'email')->first();
        $phone = $this->contacts()->where('is_primary', true)->where('type', 'phone')->first();
        return [
            'uuid' => $this->uuid,
            'email' => $this->whenNotNull($email?->value),
            'phone' => $this->whenNotNull($phone?->value),
            'roles' => $this->roles->pluck('name'),
            'permissions' => $this->permissions->pluck('name'),
        ];
    }
}
