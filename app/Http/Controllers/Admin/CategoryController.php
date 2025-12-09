<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
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
        Gate::authorize('viewAny', Category::class);

        $categories = Category::withCount('posts')
            ->orderBy('order_column')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        Gate::authorize('create', Category::class);

        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Category::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:categories,name'],
            'slug' => ['nullable', 'string', 'max:160', 'unique:categories,slug'],
            'description' => ['nullable', 'string'],
            'order_column' => ['nullable', 'integer'],
        ], [
            'name.required' => 'اسم القسم مطلوب',
            'name.max' => 'اسم القسم يجب أن لا يتجاوز 120 حرف',
            'name.unique' => 'اسم القسم مستخدم مسبقاً',
            'slug.max' => 'الرابط الدائم يجب أن لا يتجاوز 160 حرف',
            'slug.unique' => 'الرابط الدائم مستخدم مسبقاً',
            'order_column.integer' => 'الترتيب يجب أن يكون رقماً صحيحاً',
        ]);

        $validated['slug'] = $validated['slug'] ?? $this->generateArabicSlug($validated['name']);
        $validated['order_column'] = $validated['order_column'] ?? Category::max('order_column') + 1;

        $category = Category::create($validated);

        // Return JSON for AJAX requests
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء القسم بنجاح',
                'category' => $category
            ]);
        }

        return redirect()->route('admin.categories.index')
        ->with('success', 'تم إنشاء القسم بنجاح');
    }

    public function edit(Category $category): View
    {
        Gate::authorize('update', $category);

        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        Gate::authorize('update', $category);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:categories,name,' . $category->id],
            'slug' => ['nullable', 'string', 'max:160', 'unique:categories,slug,' . $category->id],
            'description' => ['nullable', 'string'],
            'order_column' => ['nullable', 'integer'],
        ], [
            'name.required' => 'اسم القسم مطلوب',
            'name.max' => 'اسم القسم يجب أن لا يتجاوز 120 حرف',
            'name.unique' => 'اسم القسم مستخدم مسبقاً',
            'slug.max' => 'الرابط الدائم يجب أن لا يتجاوز 160 حرف',
            'slug.unique' => 'الرابط الدائم مستخدم مسبقاً',
            'order_column.integer' => 'الترتيب يجب أن يكون رقماً صحيحاً',
        ]);

        if (isset($validated['slug']) && !empty($validated['slug'])) {
            $validated['slug'] = $this->generateArabicSlug($validated['slug']);
        } elseif (empty($validated['slug'])) {
            $validated['slug'] = $this->generateArabicSlug($validated['name']);
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'تم تحديث القسم بنجاح.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        Gate::authorize('delete', $category);

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'تم حذف القسم بنجاح.');
    }
}

