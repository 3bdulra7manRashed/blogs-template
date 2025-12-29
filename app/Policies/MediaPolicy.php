<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\User;

class MediaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isModerator();
    }

    public function view(User $user, Media $media): bool
    {
        return $user->isAdmin() || $media->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isModerator();
    }

    public function update(User $user, Media $media): bool
    {
        return $user->isAdmin() || $media->user_id === $user->id;
    }

    public function delete(User $user, Media $media): bool
    {
        return $user->isAdmin() || $media->user_id === $user->id;
    }
}

