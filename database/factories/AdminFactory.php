<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Admin::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fname' => fake()->firstName(),
            'lname' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'role_id' => 2, // Default to admin role
            'is_active' => true,
            'email_opt_in' => false,
        ];
    }

    /**
     * Indicate that the admin should be a system admin.
     */
    public function systemAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => 1, // System Admin
        ]);
    }

    /**
     * Indicate that the admin should be a regular admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => 2, // Admin
        ]);
    }

    /**
     * Indicate that the admin should be support staff.
     */
    public function support(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => 3, // Support
        ]);
    }

    /**
     * Indicate that the admin should be an instructor.
     */
    public function instructor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => 4, // Instructor
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
