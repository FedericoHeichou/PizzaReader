<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role_id' => 5, // user
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'all_comics' => false,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => ['role_id' => 1]);
    }

    public function manager(): static
    {
        return $this->state(fn (array $attributes) => ['role_id' => 2]);
    }

    public function editor(): static
    {
        return $this->state(fn (array $attributes) => ['role_id' => 3]);
    }

    public function checker(): static
    {
        return $this->state(fn (array $attributes) => ['role_id' => 4]);
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
