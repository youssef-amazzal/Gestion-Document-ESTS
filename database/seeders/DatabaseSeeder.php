<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            FiliereSeeder::class,
            TagSeeder::class,
            UserSeeder::class,
            FileSeeder::class,
            PrivilegeSeeder::class,
            GroupSeeder::class,
            FolderSeeder::class,
            ElementSeeder::class,
            OperationSeeder::class,
        ]);

    }
}
