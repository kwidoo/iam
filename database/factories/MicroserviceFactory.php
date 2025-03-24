<?php

namespace Database\Factories;

use App\Models\Microservice;
use Illuminate\Database\Eloquent\Factories\Factory;

class MicroserviceFactory extends Factory
{
    protected $model = Microservice::class;

    public function definition() {
        return [
            'name'     => $this->faker->unique()->word,
            'endpoint' => $this->faker->url,
            'api_key'  => $this->faker->sha256,
            'status'   => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}