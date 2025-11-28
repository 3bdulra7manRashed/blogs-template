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

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Category::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:categories,name'],
            'slug' => ['nullable', 'string', 'max:160', 'unique:categories,slug'],
            'description' => ['nullable', 'string'],
            'order_column' => ['nullable', 'integer'],
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['order_column'] = $validated['order_column'] ?? Category::max('order_column') + 1;

        Category::create($validated);

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
        ]);

        if (isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        Gate::authorize('delete', $category);

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}

