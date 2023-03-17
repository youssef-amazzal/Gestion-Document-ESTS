<?php

namespace Database\Factories;

use App\Enums\Privileges;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Privilege>
 */
class PrivilegeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $file_privileges = array_filter(Privileges::cases(), fn($privilege) => str_starts_with($privilege->value, 'file_'));
        $file_privileges = Arr::crossJoin(['file'], $file_privileges);

        $system_privileges = array_filter(Privileges::cases(), fn($privilege) => str_starts_with($privilege->value, 'system_'));
        $system_privileges = Arr::crossJoin(['system'], $system_privileges);

        $privilege = $this->faker->unique()->randomElement(array_merge($file_privileges, $system_privileges));

        return [
            'type' => $privilege[0],
            'name' => $privilege[1],
            'granted_by' => User::query()->inRandomOrder()->first(),
            'granted_to' => User::query()->inRandomOrder()->first(),
        ];
    }
}
