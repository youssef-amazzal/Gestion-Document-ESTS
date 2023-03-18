<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\Privilege;
use Illuminate\Database\Seeder;

class PrivilegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Privilege::factory()
            ->count(100)
            ->create();

    }
}
