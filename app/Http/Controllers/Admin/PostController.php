<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePostRequest;
use App\Http\Requests\Admin\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Generate a slug that preserves Arabic characters
     */
    private function generateArabicSlug(string $text): string
    {
        // Replace spaces with hyphens
        $slug = preg_replace('/\s+/', '-', trim($text));
        
        // Remove special characters but preserve Arabic (Unicode range \u0600-\u06FF), English letters, numbers, and hyphens
        $slug = preg_replace('/[^\p{Arabic}\p{L}\p{N}\-]+/u', '', $slug);
        
        // Replace multiple consecutive hyphens with single hyphen
        $slug = preg_replace('/\-+/', '-', $slug);
        
        // Trim hyphens from start and end
        $slug = trim($slug, '-');
        
        return $slug;
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Post::class);

        $query = Post::with(['author', 'categories', 'tags']);

        if ($request->has('status')) {
            match ($request->status) {
                'draft' => $query->where('is_draft', true),
                'published' => $query->published(),
                default => null,
            };
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->latest('created_at')->paginate(15)->withQueryString();

        return view('admin.posts.index', compact('posts'));
    }

    public function create(): View
    {
        Gate::authorize('create', Post::class);

        $categories = Category::orderBy('order_column')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.create', compact('categories', 'tags'));
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        Gate::authorize('create', Post::class);

        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['slug'] = $this->generateArabicSlug($data['slug'] ?? $data['title']);

        // Auto-publish if published_at is set and not explicitly marked as draft
        if (!empty($data['published_at']) && empty($data['is_draft'])) {
            $publishDate = \Carbon\Carbon::parse($data['published_at']);
            // If publish date is now or in the past, set as published
            if ($publishDate->isPast() || $publishDate->isToday()) {
                $data['is_draft'] = false;
            }
        }

        if (isset($data['featured_image'])) {
            $path = $request->file('featured_image')->store('posts', 'public');
            $data['featured_image_path'] = $path;
            unset($data['featured_image']);
        }

        $post = Post::create($data);

        if (isset($data['categories'])) {
            $post->categories()->sync($data['categories']);
        }

        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        return redirect()->route('admin.posts.index')
        ->with('success', 'تم إنشاء المقال بنجاح');
    }

    public function show(Post $post): View
    {
        Gate::authorize('view', $post);

        $post->load(['author', 'categories', 'tags']);

        return view('admin.posts.show', compact('post'));
    }

    public function edit(Post $post): View
    {
        Gate::authorize('update', $post);

        $post->load(['categories', 'tags']);
        $categories = Category::orderBy('order_column')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        Gate::authorize('update', $post);

        $data = $request->validated();

        if (isset($data['slug']) && !empty($data['slug'])) {
            $data['slug'] = $this->generateArabicSlug($data['slug']);
        } elseif (empty($data['slug'] ?? '')) {
            // If slug is empty, generate from title
            $data['slug'] = $this->generateArabicSlug($data['title']);
        }

        // Auto-publish if published_at is set and not explicitly marked as draft
        if (!empty($data['published_at']) && empty($data['is_draft'])) {
            $publishDate = \Carbon\Carbon::parse($data['published_at']);
            // If publish date is now or in the past, set as published
            if ($publishDate->isPast() || $publishDate->isToday()) {
                $data['is_draft'] = false;
            }
        }

        if (isset($data['featured_image'])) {
            if ($post->featured_image_path) {
                Storage::disk('public')->delete($post->featured_image_path);
            }
            $path = $request->file('featured_image')->store('posts', 'public');
            $data['featured_image_path'] = $path;
            unset($data['featured_image']);
        }

        $post->update($data);

        if (isset($data['categories'])) {
            $post->categories()->sync($data['categories']);
        } else {
            $post->categories()->detach();
        }

        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        } else {
            $post->tags()->detach();
        }

        return redirect()->route('admin.posts.index')
        ->with('success', 'تم تحديث المقال بنجاح');
    }

    public function destroy(Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);

        if ($post->featured_image_path) {
            Storage::disk('public')->delete($post->featured_image_path);
        }

        $post->delete();

        return redirect()->route('admin.posts.index')
        ->with('success', 'تم حذف المقال بنجاح');
    }
}

