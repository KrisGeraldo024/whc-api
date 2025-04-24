<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Role;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'name' => $this->faker->randomElement(['Admin', 'Editor', 'Customer']),
            'identifier' => $this->faker->slug(),
            'type' => $this->faker->randomElement(['admin', 'editor', 'customer']),
            'permissions' => json_encode([]), // Adjust permissions as needed
        ];
    }
}

