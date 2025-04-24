<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use App\Models\UserDetail; // Import the UserDetail model
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            // 'name' => $this->faker->name(),
            'id' => (string) Str::uuid(),
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => 'admin', // You should use bcrypt for the password
            'remember_token' => Str::random(10),
            'enabled' => 1,
            'role_id' => Role::factory()
        ];
    }

    /**
     * Configure the factory to automatically populate the user_details table.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            $firstName = $this->faker->firstName();
            $lastName = $this->faker->lastName();

            // Automatically create user_details after creating the user
            UserDetail::create([
                'user_id' => $user->id, // Associate the user_id
                'member_id' => Str::uuid(), // Or any logic for generating a member ID
                'first_name' => $firstName,
                'last_name' => $lastName,
                'full_name' => $firstName . ' ' . $lastName, // Use the user's full name
                'contact_number' => $this->faker->phoneNumber(),
                'slug' => Str::slug($user->name),
            ]);
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
