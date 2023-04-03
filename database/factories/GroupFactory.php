<?php

namespace Database\Factories;

use App\Enums\Privileges;
use App\Enums\Roles;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'Professors',
            'Students',
        ];

        return [
            'name' => $this->faker->unique()->randomElement($groups),
            'user_id' => User::find(1),
        ];
    }

    public function configure(): GroupFactory
    {
        return $this->afterCreating(function ($group) {
            if ($group->name === 'Professors') {
                $group->users()->attach(User::query()->where('role', Roles::PROFESSOR)->get());
            }
            elseif ($group->name === 'Students') {
                $group->users()->attach(User::query()->where('role', Roles::STUDENT)->get());
            }
        });
    }
}
