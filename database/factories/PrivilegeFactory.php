<?php

namespace Database\Factories;

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
        $file_privileges = [
            // File privileges
            'create',
            'read',
            'update',
            'delete',
            'share',
            'download',
            'upload',
        ];

        $file_privileges = Arr::crossJoin(['file'], $file_privileges);

        $system_privileges = [
            // System privileges
            'grant',
            'revoke',
            'create_group',
            'delete_group',
            'add_user_to_group',
            'remove_user_from_group',
            'create_user',
            'edit_user',
            'delete_user',
            'backup',
            'restore',
        ];

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
