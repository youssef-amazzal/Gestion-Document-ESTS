<?php

namespace Database\Seeders;

use App\Models\Folder;
use Illuminate\Database\Seeder;

class FolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Folder::factory()->count(10)->create();
        Folder::factory()->count(10)->create();
        Folder::factory()->count(10)->create();
        Folder::factory()->count(10)->create();
        Folder::factory()->count(10)->create();
    }
}
