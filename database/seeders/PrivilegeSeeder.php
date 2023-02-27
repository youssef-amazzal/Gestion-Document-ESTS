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
            ->count(16)
            ->state(function (array $attributes) {
                if ($attributes['type'] === 'file') {
                    return [
                        'granted_on' => File::query()->inRandomOrder()->first()
                    ];
                } else {
                    return [
                        'granted_on' => null
                    ];
                }
            })
            ->create();

    }
}
