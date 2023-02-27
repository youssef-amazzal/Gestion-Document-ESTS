<?php

namespace Database\Seeders;

use App\Models\File;
use Illuminate\Database\Seeder;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //truncate existing files records to start fresh
//        File::truncate();

        //create a bunch of dummy files records
        File::factory(100)->create();
    }
}
