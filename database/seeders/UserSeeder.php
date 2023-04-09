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
                    'name' => 'Public',
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
        $stu->filieres()->attach(Filiere::query()->where('abbreviation', '=', 'GL')->first(), ['year' => now()->year]);

        $stu = User::create([
            'email' => 'brahim_boujdaa@um5.ac.ma',
            'first_name' => 'Brahim',
            'last_name' => 'Boujdaa',
            'password' => Hash::make('brahim123'),
            'role' => Roles::STUDENT,
        ]);
        $stu->filieres()->attach(Filiere::query()->where('abbreviation', '=', 'GL')->first(), ['year' => now()->year]);

        $prof = User::create([
            'email' => 'Toufik@um5.ac.ma',
            'first_name' => 'Toufik',
            'last_name' => 'Fouad',
            'password' => Hash::make('toufik123'),
            'role' => Roles::PROFESSOR,
        ]);

        $prof->filieres()->attach(Filiere::query()->where('abbreviation', '=', 'GL')->first(), ['year' => now()->year]);
        $prof->filieres()->attach(Filiere::query()->where('abbreviation', '=', 'ARI')->first(), ['year' => now()->year]);

        $prof->save();


        $prof = User::create([
            'email' => 'Prof_1@um5.ac.ma',
            'first_name' => 'Prof',
            'last_name' => '1',
            'password' => Hash::make('prof123'),
            'role' => Roles::PROFESSOR,
        ]);

        $prof->filieres()->attach(Filiere::query()->where('abbreviation', '=', 'GL')->first(), ['year' => now()->year]);
        $prof->filieres()->attach(Filiere::query()->inRandomOrder()->take(2)->get(), ['year' => now()->year]);
        $prof->save();

        User::factory()
            ->count(200)
            ->create();
    }
}
