<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition() {
        return [
            'name'       => $this->faker->unique()->word,
            'guard_name' => $this->faker->randomElement(['web', 'api']),
        ];
    }
}