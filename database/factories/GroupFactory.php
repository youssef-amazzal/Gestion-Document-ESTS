<?php

namespace Database\Factories;

use App\Models\Privilege;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $groups = [
            'Admin',
            'Teacher',
            'Student',
        ];
        return [
            'name' => $this->faker->unique()->randomElement($groups),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($group) {
            if ($group->name === 'Admin') {
                $group->privileges()->attach(Privilege::all());
            }

            elseif ($group->name === 'Teacher') {
                return $group->privileges()->attach(Privilege::query()->where('type', '==', 'file')->get());
            }

            else {
                return $group->privileges()->attach(Privilege::query()->where('name', '==', 'read')->get());
            }
        });
    }
}
