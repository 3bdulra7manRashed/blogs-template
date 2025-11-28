<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        $filename = fake()->lexify('writer-media-????') . '.jpg';

        return [
            'user_id' => User::factory(),
            'filename' => $filename,
            'disk' => 'public',
            'path' => "media/{$filename}",
            'size' => fake()->numberBetween(120_000, 1_200_000),
            'mime_type' => 'image/jpeg',
            'caption' => fake()->sentence(6),
            'alt_text' => fake()->sentence(4),
            'meta' => [
                'original_url' => fake()->imageUrl(),
            ],
        ];
    }
}

