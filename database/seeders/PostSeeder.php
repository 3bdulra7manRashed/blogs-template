<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();
        $tags = Tag::all();

        if ($users->isEmpty() || ($categories->isEmpty() && $tags->isEmpty())) {
            Post::factory(8)->create();

            return;
        }

        Post::factory(20)
            ->state(fn () => ['user_id' => $users->random()->id])
            ->create()
            ->each(function (Post $post) use ($categories, $tags) {
                if ($categories->isNotEmpty()) {
                    $categoryIds = $categories->pluck('id')
                        ->shuffle()
                        ->take(min(3, max(1, $categories->count())))
                        ->all();

                    $post->categories()->sync($categoryIds);
                }

                if ($tags->isNotEmpty()) {
                    $tagIds = $tags->pluck('id')
                        ->shuffle()
                        ->take(min(4, max(1, $tags->count())))
                        ->all();

                    $post->tags()->sync($tagIds);
                }

                if ($post->published_at && $post->published_at->isPast()) {
                    $post->update(['is_draft' => false]);
                }
            });
    }
}

