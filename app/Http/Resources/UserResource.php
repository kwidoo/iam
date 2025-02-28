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
        return [
            'uuid' => $this->resource->uuid,
            'primary_email' => $this->resource->email?->email,
            'roles' => $this->whenLoaded('roles', $this->resource->roles->map(fn ($role) => [
                'name' => $role->name,
                'permissions' => $role->permissions->map(fn ($permission) => [
                    'name' => $permission->name,
                ])
            ])),

        ];
    }
}
