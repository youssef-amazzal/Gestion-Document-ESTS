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


        $class = Filiere::query()->inRandomOrder()->first();
        $class = $class->abbreviation . '-' . $class->promotion;

        try {
            return [
                'name' => $this->faker->unique()->randomElement($groups),
                'user_id' => User::query()->inRandomOrder()->first()->id,
            ];
        } catch (OverflowException) {
            return [
                'name' => $class,
                'user_id' => User::query()->inRandomOrder()->first()->id,
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
