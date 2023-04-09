<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\Filiere;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Group::factory()
            ->count(2)
            ->create();

        $profs = User::query()->where('role', '=', Roles::PROFESSOR)->get();
        foreach ($profs as $prof) {

            $filieres = $prof->filieres;
            $filieres->each(function (Filiere $filiere) use ($prof) {
                $group = $prof->ownedGroups()->create(['name' => $filiere->name, 'user_id' => $prof->id]);
                $students = $filiere->students()->get();
                $group->users()->attach($students);
        });
        }
    }
}
