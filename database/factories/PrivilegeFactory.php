<?php

namespace Database\Factories;

use App\Enums\Privileges;
use App\Models\Group;
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

        $grantee_type = [User::class, Group::class];
        $grantee_type = $grantee_type[array_rand($grantee_type)];

        return [
            'type' => $privilege[0],
            'action' => $privilege[1],
            'grantor_id' => User::query()->inRandomOrder()->first(),
            'grantee_type' => $grantee_type,
            'grantee_id' => $grantee_type::query()->inRandomOrder()->first(),
        ];
    }
}
