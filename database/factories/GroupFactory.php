<?php

namespace Database\Factories;

use App\Models\Filiere;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use OverflowException;

/**
 * @extends Factory<Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $groups = [
            'Admins',
            'Staff',
            'Teachers',
            'Students',
            'First Year',
            'Second Year',
            'Clubs',
            'Robotic Club',
            'Art Club',
            'Public'
        ];

        $class = Filiere::query()->inRandomOrder()->first()->abbreviation;
        $class = $class . '-' . $this->faker->randomElement(['A', 'B']);

        try {
            return [
                'name' => $this->faker->unique()->randomElement($groups),
            ];
        } catch (OverflowException) {
            return [
                'name' => $class,
            ];
        }
    }

    public function configure(): GroupFactory
    {
        return $this->afterCreating(function ($group) {
            $group->memebers()->attach(User::query()->inRandomOrder()
                                                    ->limit(5)
                                                    ->get(),
                                                    ['created_at' => now(),
                                                     'updated_at' => now()]);
        });
    }
}
