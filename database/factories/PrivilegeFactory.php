<?php

namespace Database\Factories;

use App\Enums\Privileges;
use App\Models\File;
use App\Models\Folder;
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
        $file_privileges    = Arr::crossJoin(['file'], Privileges::filePrivileges());
        $system_privileges  = Arr::crossJoin(['system'], Privileges::systemPrivileges());

        $privilege = $this->faker->randomElement(array_merge($file_privileges, $system_privileges));

        $target_type = null;
        if ($privilege[0] === 'file') {
            $target_type = [File::class, Folder::class];
            $target_type = $target_type[array_rand($target_type)];
        }

        $grantee_type = [User::class, Group::class];
        $grantee_type = $grantee_type[array_rand($grantee_type)];



        return [
            'type' => $privilege[0],
            'action' => $privilege[1],
            'grantor_id' => User::query()->inRandomOrder()->first(),
            'target_type' => $target_type,
            'target_id' => $target_type ? $target_type::query()->inRandomOrder()->first() : null,
            'grantee_type' => $grantee_type,
            'grantee_id' => $grantee_type::query()->inRandomOrder()->first(),
        ];
    }
}
