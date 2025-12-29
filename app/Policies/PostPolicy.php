<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Post $post): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isModerator();
    }

    public function update(User $user, Post $post): bool
    {
        return $user->isAdmin() || $post->user_id === $user->id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->isAdmin() || ($user->isModerator() && $post->user_id === $user->id);
    }

    public function restore(User $user, Post $post): bool
    {
        return $this->delete($user, $post);
    }

    public function forceDelete(User $user, Post $post): bool
    {
        return $user->isAdmin();
    }

    public function publish(User $user, Post $post): bool
    {
        return $user->isAdmin() || ($user->isModerator() && $post->user_id === $user->id);
    }
}

