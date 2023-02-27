<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\User;
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
//        $file_name = fake()->file('/tmp', 'storage', false);
        return [
            'name' => 'test',
            'description' => fake()->sentence(),
            'size' => fake()->numberBetween(100, 1000000),
            'mime_type' => fake()->mimeType(),
            'path' => 'storage/test',
            'owner_id' => User::query()->inRandomOrder()->first(),
        ];
    }
}
