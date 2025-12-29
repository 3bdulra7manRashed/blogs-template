<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\MediaPolicy;
use App\Policies\PostPolicy;
use App\Policies\TagPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Post::class => PostPolicy::class,
        Category::class => CategoryPolicy::class,
        Tag::class => TagPolicy::class,
        Media::class => MediaPolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Only Super Admin (مدير النظام) can manage users
        Gate::define('manage-users', fn (User $user) => $user->isSuperAdmin());

        // Moderators (مشرف) and Super Admins can manage content
        Gate::define('manage-content', fn (User $user) => 
            $user->hasRole('moderator') || $user->isAdmin() || $user->isSuperAdmin()
        );

        // Super Admin bypass for all gates
        Gate::before(function (User $user, string $ability = '') {
            return $user->isSuperAdmin() ? true : null;
        });
    }
}

