<?php

namespace Database\Factories;

use App\Models\Folder;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Folder>
 */
class FolderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $parent_folder = $this->faker->randomElement([Folder::query()->inRandomOrder()->first(), null]);
        $owner = $parent_folder && $parent_folder->owner ? $parent_folder->owner : User::query()->inRandomOrder()->first();
        $path = $parent_folder && $parent_folder->owner ? $parent_folder->path . '/' . $parent_folder->name : 'app/public';

        return [
            'name' => $this->faker->unique()->word,
            'description' => $this->faker->sentence,
            'path' => $path,
            'owner_id' => $owner,
            'parent_folder_id' => $parent_folder,
        ];
    }

    public function configure(): FolderFactory
    {
        return $this->afterCreating(function ($file) {
            $file->tags()->attach(Tag::query()->inRandomOrder()
                ->limit(rand(0, 5))
                ->get(),
                ['created_at' => now(),
                    'updated_at' => now()]);
        });
    }
}
