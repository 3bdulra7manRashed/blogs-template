<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TagController extends Controller
{
    /**
     * Generate a slug that preserves Arabic characters
     */
    protected function generateArabicSlug(string $text): string
    {
        // Replace spaces with dashes
        $slug = str_replace(' ', '-', trim($text));
        
        // Remove illegal characters but KEEP Arabic letters, numbers, and dashes
        // \p{L} matches any unicode letter (including Arabic)
        // \p{N} matches any number
        $slug = preg_replace('/[^\p{L}\p{N}\-]+/u', '', $slug);
        
        // Remove multiple dashes
        $slug = preg_replace('/-+/', '-', $slug);
        
        // Trim dashes from start and end
        $slug = trim($slug, '-');
        
        return $slug;
    }

    public function index(): View
    {
        Gate::authorize('viewAny', Tag::class);

        $tags = Tag::withCount('posts')
            ->orderBy('name')
            ->get();

        return view('admin.tags.index', compact('tags'));
    }

    public function create(): View
    {
        Gate::authorize('create', Tag::class);

        return view('admin.tags.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Tag::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:tags,name'],
            'slug' => ['nullable', 'string', 'max:160', 'unique:tags,slug'],
        ], [
            'name.required' => 'اسم الوسم مطلوب',
            'name.max' => 'اسم الوسم يجب أن لا يتجاوز 120 حرف',
            'name.unique' => 'اسم الوسم مستخدم مسبقاً',
            'slug.max' => 'الرابط الدائم يجب أن لا يتجاوز 160 حرف',
            'slug.unique' => 'الرابط الدائم مستخدم مسبقاً',
        ]);

        $validated['slug'] = $validated['slug'] ?? $this->generateArabicSlug($validated['name']);

        $tag = Tag::create($validated);

        // Return JSON for AJAX requests
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الوسم بنجاح',
                'tag' => $tag
            ]);
        }

        return redirect()->route('admin.tags.index')
        ->with('success', 'تم إنشاء الوسم بنجاح');
    }

    public function edit(Tag $tag): View
    {
        Gate::authorize('update', $tag);

        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag): RedirectResponse
    {
        Gate::authorize('update', $tag);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:tags,name,' . $tag->id],
            'slug' => ['nullable', 'string', 'max:160', 'unique:tags,slug,' . $tag->id],
        ], [
            'name.required' => 'اسم الوسم مطلوب',
            'name.max' => 'اسم الوسم يجب أن لا يتجاوز 120 حرف',
            'name.unique' => 'اسم الوسم مستخدم مسبقاً',
            'slug.max' => 'الرابط الدائم يجب أن لا يتجاوز 160 حرف',
            'slug.unique' => 'الرابط الدائم مستخدم مسبقاً',
        ]);

        if (isset($validated['slug']) && !empty($validated['slug'])) {
            $validated['slug'] = $this->generateArabicSlug($validated['slug']);
        } elseif (empty($validated['slug'])) {
            $validated['slug'] = $this->generateArabicSlug($validated['name']);
        }

        $tag->update($validated);

        return redirect()->route('admin.tags.index')
            ->with('success', 'تم تحديث الوسم بنجاح.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        Gate::authorize('delete', $tag);

        $tag->delete();

        return redirect()->route('admin.tags.index')
            ->with('success', 'تم حذف الوسم بنجاح.');
    }
}

