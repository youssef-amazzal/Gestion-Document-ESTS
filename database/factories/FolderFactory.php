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
        $space = $parent_folder && $parent_folder->owner ? $parent_folder->space : $owner->spaces()->inRandomOrder()->first();

        return [
            'name' => $this->faker->unique()->word,
            'description' => $this->faker->sentence,
            'owner_id' => $owner,
            'space_id' => $space,
            'parent_folder_id' => $parent_folder,
        ];
    }

    public function configure(): FolderFactory
    {
        return $this->afterCreating(function ($folder) {
            $folder->tags()->create(['name' => $this->faker->word, 'user_id' => $folder->owner->id],['created_at' => now(),'updated_at' => now()]);
        });
    }
}
