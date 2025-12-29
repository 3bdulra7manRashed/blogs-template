<?php

use App\Http\Controllers\Api\PostLikeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public blog routes
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/post/{slug}', [PostController::class, 'show'])->name('post.show');
Route::get('/category/{slug}', [PostController::class, 'category'])->name('category.show');
Route::get('/tag/{slug}', [PostController::class, 'tag'])->name('tag.show');
Route::get('/search', [PostController::class, 'search'])->name('search');
Route::get('/posts/most-liked', [PostController::class, 'mostLiked'])->name('posts.most-liked');
Route::get('/posts/most-read', [PostController::class, 'mostRead'])->name('posts.most-read');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'sendContact'])->name('contact.send');

// API routes for post likes (no authentication required for guests)
Route::post('/api/posts/{post}/like', [PostLikeController::class, 'toggle'])->name('api.posts.like');
Route::get('/api/posts/{post}/likes', [PostLikeController::class, 'count'])->name('api.posts.likes');

// Authenticated routes
Route::get('/dashboard', function () {
    return redirect()->route('profile.edit');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/bio', [ProfileController::class, 'updateBio'])->name('profile.updateBio');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // CKEditor image upload route
    Route::post('/ckeditor/upload', [\App\Http\Controllers\CkeditorController::class, 'upload'])->name('ckeditor.upload');
});

// Admin routes - Protected by role:admin|moderator middleware
// Both مدير النظام (admin) and مشرف (moderator) can access content management
Route::prefix('admin')->middleware(['auth', 'role:admin|moderator'])->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class);
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->except(['show']);
    Route::resource('tags', \App\Http\Controllers\Admin\TagController::class)->except(['show']);
    Route::resource('media', \App\Http\Controllers\Admin\MediaController::class)->except(['show', 'edit', 'update']);
    Route::post('media/upload', [\App\Http\Controllers\Admin\MediaController::class, 'store'])->name('media.upload');
    Route::post('upload-image', [\App\Http\Controllers\Admin\MediaController::class, 'upload'])->name('upload.image');
});

// User management routes - Only Super Admin (مدير النظام) can access
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::post('users/{user}/promote', [\App\Http\Controllers\Admin\UserController::class, 'promote'])->name('users.promote');
    Route::post('users/{user}/demote', [\App\Http\Controllers\Admin\UserController::class, 'demote'])->name('users.demote');
});

// Super-admin user management routes (soft delete, restore, force delete)
Route::prefix('admin')->middleware(['auth', 'superadmin'])->name('admin.')->group(function () {
    Route::delete('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    Route::post('users/{id}/restore', [\App\Http\Controllers\Admin\UserController::class, 'restore'])->name('users.restore');
    Route::delete('users/{id}/force-delete', [\App\Http\Controllers\Admin\UserController::class, 'forceDelete'])->name('users.forceDelete');
});

// User management routes - Only Super Admin (using superadmin middleware)
Route::prefix('admin')->middleware(['auth', 'superadmin'])->name('admin.')->group(function () {
    Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    Route::post('users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
});

require __DIR__.'/auth.php';
