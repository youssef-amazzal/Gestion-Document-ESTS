<?php

namespace Database\Factories;

use App\Enums\Roles;
use App\Models\Element;
use App\Models\Filiere;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Element>
 */
class ElementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => 'Element ' . $this->faker->numberBetween(1, 100),
            'filiere_id' => Filiere::query()->inRandomOrder()->first(),
            'professor_id' => User::query()->where('role', Roles::PROFESSOR)->inRandomOrder()->first(),
        ];
    }
}
