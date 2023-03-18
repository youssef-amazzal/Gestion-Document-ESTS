<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->create([
            'email' => 'admin_ests@um5.ac.ma',
            'password' => bcrypt('admin_ests'),
            'role' => Roles::ADMIN,
            'first_name' => 'Admin',
            'last_name' => 'ESTS',
        ]);

        User::factory()
            ->count(200)
            ->create();
    }
}
