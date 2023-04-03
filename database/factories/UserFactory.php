<?php

namespace Database\Factories;

use App\Enums\Roles;
use App\Models\Filiere;
use App\Models\Space;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $dice = rand(1, 6) % 6;

        return [
            'first_name'        => fake()->firstName(),
            'last_name'         => fake()->lastName(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => null,
            'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role'              => ($dice !== 0) ? Roles::STUDENT : Roles::PROFESSOR,
        ];
    }

    public function configure(): UserFactory
    {
        return $this->afterCreating(function ($user) {
            if ($user->role === Roles::STUDENT) {
                $user->filieres()->attach(Filiere::query()->inRandomOrder()->first(), ['year' => now()->year]);
            }
            else if ($user->role === Roles::PROFESSOR) {
                $user->filieres()->attach(Filiere::query()->inRandomOrder()->take(rand(1, 3))->get(), ['year' => now()->year]);
            }
        });
    }
}
