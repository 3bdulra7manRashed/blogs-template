<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'excerpt' => fake()->paragraph(),
            'content' => collect(fake()->paragraphs(mt_rand(4, 8)))
                ->map(fn ($paragraph) => "<p>{$paragraph}</p>")
                ->implode("\n"),
            'featured_image_path' => 'https://picsum.photos/1200/630?random=' . fake()->numberBetween(1, 1000),
            'featured_image_alt' => fake()->sentence(6),
            'is_draft' => fake()->boolean(30),
            'published_at' => fake()->dateTimeBetween('-2 months', '+1 month'),
            'meta' => [
                'title' => $title,
                'description' => fake()->sentence(12),
            ],
        ];
    }

    public function drafted(): self
    {
        return $this->state(fn () => [
            'is_draft' => true,
            'published_at' => null,
        ]);
    }

    public function published(): self
    {
        return $this->state(fn () => [
            'is_draft' => false,
            'published_at' => now(),
        ]);
    }
}

