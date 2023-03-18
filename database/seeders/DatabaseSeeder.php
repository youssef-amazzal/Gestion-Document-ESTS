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
    public function run(): void
    {
        $this->call([
            FiliereSeeder::class,
            UserSeeder::class,
            ElementSeeder::class,
            TagSeeder::class,
            FolderSeeder::class,
            FileSeeder::class,
            GroupSeeder::class,
            PrivilegeSeeder::class,
            OperationSeeder::class,
        ]);

    }
}
