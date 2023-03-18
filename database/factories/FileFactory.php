<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\Folder;
use App\Models\Tag;
use App\Models\User;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $parent_folder = $this->faker->randomElement([Folder::query()->inRandomOrder()->first(), null]);
        $owner = $parent_folder ? $parent_folder->owner : User::query()->inRandomOrder()->first();
        $path = $parent_folder ? $parent_folder->path . '/' . $parent_folder->name : 'app/public';

        $extension = $this->faker->fileExtension();
        $mimeType = MimeType::fromExtension($extension);

        return [
            'name' => fake()->word() . '.' . $extension,
            'description' => fake()->sentence(),
            'size' => fake()->numberBetween(100, 1000000),
            'mime_type' => $mimeType,
            'path' => $path,
            'owner_id' => $owner,
            'parent_folder_id' => $parent_folder,
        ];
    }

    public function configure(): FileFactory
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
