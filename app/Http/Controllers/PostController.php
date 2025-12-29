<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $posts = Post::with(['author', 'categories', 'tags'])
            ->published()
            ->latest('published_at')
            ->paginate(9);

        $recentPosts = Post::published()
            ->latest('published_at')
            ->limit(5)
            ->get();

        $categories = Category::withCount('posts')
            ->orderBy('order_column')
            ->get();

        return view('posts.index', compact('posts', 'recentPosts', 'categories'));
    }

    public function show(string $slug): View
    {
        $post = Post::with(['author', 'categories', 'tags'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();
        
        // Increment views count
        $post->increment('views');

        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->whereHas('categories', function ($query) use ($post) {
                $query->whereIn('categories.id', $post->categories->pluck('id'));
            })
            ->limit(3)
            ->get();

        if ($relatedPosts->isEmpty()) {
            $relatedPosts = Post::published()
                ->where('id', '!=', $post->id)
                ->limit(3)
                ->get();
        }

        $previousPost = Post::published()
            ->where('published_at', '<', $post->published_at)
            ->orderBy('published_at', 'desc')
            ->first();

        $nextPost = Post::published()
            ->where('published_at', '>', $post->published_at)
            ->orderBy('published_at', 'asc')
            ->first();

        $recentPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->limit(5)
            ->get();

        $categories = Category::withCount('posts')
            ->orderBy('order_column')
            ->get();

        return view('posts.show', compact('post', 'relatedPosts', 'previousPost', 'nextPost', 'recentPosts', 'categories'));
    }

    public function category(string $slug, Request $request): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = Post::with(['author', 'categories', 'tags'])
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('categories.id', $category->id);
            })
            ->published()
            ->latest('published_at')
            ->paginate(9);

        $recentPosts = Post::published()
            ->latest('published_at')
            ->limit(5)
            ->get();

        $categories = Category::withCount('posts')
            ->orderBy('order_column')
            ->get();

        return view('posts.category', compact('category', 'posts', 'recentPosts', 'categories'));
    }

    public function tag(string $slug, Request $request): View
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $posts = Post::with(['author', 'categories', 'tags'])
            ->whereHas('tags', function ($query) use ($tag) {
                $query->where('tags.id', $tag->id);
            })
            ->published()
            ->latest('published_at')
            ->paginate(9);

        $recentPosts = Post::published()
            ->latest('published_at')
            ->limit(5)
            ->get();

        $categories = Category::withCount('posts')
            ->orderBy('order_column')
            ->get();

        return view('posts.tag', compact('tag', 'posts', 'recentPosts', 'categories'));
    }

    public function search(Request $request): View
    {
        $query = $request->get('q', '');

        $posts = Post::with(['author', 'categories', 'tags'])
            ->published()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('title', 'like', "%{$query}%")
                        ->orWhere('excerpt', 'like', "%{$query}%")
                        ->orWhere('content', 'like', "%{$query}%");
                });
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        $recentPosts = Post::published()
            ->latest('published_at')
            ->limit(5)
            ->get();

        $categories = Category::withCount('posts')
            ->orderBy('order_column')
            ->get();

        return view('posts.search', compact('posts', 'query', 'recentPosts', 'categories'));
    }

    public function mostLiked(Request $request): View
    {
        $posts = Post::with(['author', 'categories', 'tags'])
            ->published()
            ->orderBy('likes_count', 'desc')
            ->paginate(9);

        $recentPosts = Post::published()
            ->latest('published_at')
            ->limit(5)
            ->get();

        $categories = Category::withCount('posts')
            ->orderBy('order_column')
            ->get();

        return view('posts.most-liked', compact('posts', 'recentPosts', 'categories'));
    }

    public function mostRead(Request $request): View
    {
        $posts = Post::with(['author', 'categories', 'tags'])
            ->published()
            ->orderBy('views', 'desc')
            ->paginate(9);

        $recentPosts = Post::published()
            ->latest('published_at')
            ->limit(5)
            ->get();

        $categories = Category::withCount('posts')
            ->orderBy('order_column')
            ->get();

        return view('posts.most-read', compact('posts', 'recentPosts', 'categories'));
    }
}

