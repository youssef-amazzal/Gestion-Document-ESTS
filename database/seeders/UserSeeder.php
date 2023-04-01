<?php

namespace Database\Seeders;

use App\Enums\Privileges;
use App\Enums\Roles;
use App\Models\File;
use App\Models\Filiere;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::query()->create([
            'email' => 'admin_ests@um5.ac.ma',
            'password' => bcrypt('admin_ests'),
            'role' => Roles::ADMIN,
            'first_name' => 'Admin',
            'last_name' => 'ESTS',
        ]);

        $admin->spaces()->createMany(
            [
                [
                    'name' => 'Espace Personnel',
                    'is_permanent' => true,
                ],
                [
                    'name' => 'Public',
                    'is_permanent' => true,
                ],
            ]
        );

        foreach (Privileges::getAdminPrivileges() as $privilege) {
            $admin->privileges()->create([
                'action' => $privilege,
                'grantee_id' => $admin->id,
                'grantee_type' => $admin::class,
                'type' => Privileges::getType($privilege),
            ]);
        }

        $stu = User::create([
            'email' => 'youssef_amazzal@um5.ac.ma',
            'first_name' => 'Youssef',
            'last_name' => 'Amazzal',
            'password' => Hash::make('youssef123'),
            'role' => Roles::STUDENT,
        ]);
        $stu->spaces()->create(['name' => 'Espace Personnel', 'is_permanent' => true,]);
        $stu->filieres()->attach(Filiere::query()->where('abbreviation', '=', 'GL')->first(), ['year' => now()->year]);

        $prof = User::create([
            'email' => 'Toufik@um5.ac.ma',
            'first_name' => 'Toufik',
            'last_name' => 'Toufik',
            'password' => Hash::make('toufik123'),
            'role' => Roles::PROFESSOR,
        ]);

        $prof->filieres()->attach(Filiere::query()->where('abbreviation', '=', 'GL')->first(), ['year' => now()->year]);
        $prof->spaces()->create(['name' => 'Espace Personnel', 'is_permanent' => true]);
        $prof->spaces()->create(['name' => 'Java Avanée']);

        foreach ($prof->spaces() as $space) {
            $space->folders()->saveMany(Folder::factory()->afterCreating(function (Folder $folder) {
                $folder->files()->saveMany(File::factory()->count(5)->create());
            })->count(3)->create(['owner_id' => $prof->id, 'space_id' => $space->id,]));
        }

        User::factory()
            ->count(200)
            ->create();
    }
}
