<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can delete (soft delete) another user.
     */
    public function delete(User $actor, User $target): bool
    {
        // Only super-admin can delete
        if (!$actor->isSuperAdmin()) {
            return false;
        }

        // Prevent deleting the super admin (id = 1)
        if ($target->id === 1) {
            return false;
        }

        // Prevent deleting the placeholder account
        if ($target->isDeletedUserPlaceholder()) {
            return false;
        }

        // Prevent deleting yourself
        if ($actor->id === $target->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the user can restore a soft-deleted user.
     */
    public function restore(User $actor, User $target): bool
    {
        // Only super-admin can restore
        if (!$actor->isSuperAdmin()) {
            return false;
        }

        // Must be soft-deleted to restore
        return $target->trashed();
    }

    /**
     * Determine if the user can permanently delete (force delete) a user.
     */
    public function forceDelete(User $actor, User $target): bool
    {
        // Only super-admin can force delete
        if (!$actor->isSuperAdmin()) {
            return false;
        }

        // Prevent force deleting the super admin (id = 1)
        if ($target->id === 1) {
            return false;
        }

        // Prevent force deleting the placeholder account
        if ($target->isDeletedUserPlaceholder()) {
            return false;
        }

        // Prevent force deleting yourself
        if ($actor->id === $target->id) {
            return false;
        }

        return true;
    }
}

