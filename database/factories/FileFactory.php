<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\Folder;
use App\Models\Tag;
use App\Models\User;
use App\Traits\PathTrait;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<File>
 */
class FileFactory extends Factory
{
    use PathTrait;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $parent_folder = $this->faker->randomElement([Folder::query()->inRandomOrder()->first(), null]);
        $owner = $parent_folder ? $parent_folder->owner : User::query()->inRandomOrder()->first();
        $space = $parent_folder ? $parent_folder->space : $owner->spaces()->inRandomOrder()->first();

        $extension = $this->faker->fileExtension();
        $mimeType = MimeType::fromExtension($extension);

        return [
            'name' => fake()->word() . '.' . $extension,
            'description' => fake()->sentence(),
            'size' => fake()->numberBetween(100, 1000000),
            'mime_type' => $mimeType,
            'owner_id' => $owner,
            'space_id' => $space,
            'parent_folder_id' => $parent_folder,
        ];
    }

    public function configure(): FileFactory
    {
        return $this->afterCreating(function ($file) {
            $file->tags()->attach(Tag::factory(['name' => $this->faker->word, 'user_id' => $file->owner->id])->count(5)->create(),
                                  ['created_at' => now(),
                                   'updated_at' => now()]);
        });
    }
}
