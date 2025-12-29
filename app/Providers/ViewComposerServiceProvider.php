<?php

namespace App\Providers;

use App\Models\Post;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Share trending posts data with all views
        View::composer('*', function ($view) {
            $recentPosts = Post::published()
                ->latest('published_at')
                ->limit(5)
                ->get();

            $mostLikedPosts = Post::published()
                ->orderBy('likes_count', 'desc')
                ->limit(5)
                ->get();

            $mostReadPosts = Post::published()
                ->orderBy('views', 'desc')
                ->limit(5)
                ->get();

            $view->with([
                'trendingRecentPosts' => $recentPosts,
                'trendingMostLikedPosts' => $mostLikedPosts,
                'trendingMostReadPosts' => $mostReadPosts,
            ]);
        });
    }
}
