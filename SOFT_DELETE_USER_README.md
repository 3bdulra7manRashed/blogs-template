# Soft Delete User Workflow - Implementation Guide

## Overview

This implementation provides a robust soft-delete workflow for users in Laravel 12. Only super-admins can soft-delete or permanently delete users. When a user is soft-deleted, all their posts/articles are automatically reassigned to a special "Deleted User" placeholder account. All actions are auditable.

## Features

- ✅ Soft deletes with `deleted_at` column
- ✅ Super-admin authorization (boolean `is_super_admin` column)
- ✅ Automatic post reassignment to placeholder account
- ✅ Audit logging for all delete/restore/force-delete actions
- ✅ Protection against deleting placeholder account
- ✅ Restore functionality (posts remain with placeholder)
- ✅ Force delete (permanent deletion) with confirmation
- ✅ Comprehensive tests

## Installation Steps

### 1. Run Migrations

```bash
php artisan migrate
```

This will create:
- `deleted_at` column in `users` table
- `is_super_admin` boolean column in `users` table
- `audit_logs` table

### 2. Seed Placeholder User

```bash
php artisan db:seed --class=DeletedUserSeeder
```

This creates the "Deleted User" placeholder account with email `deleted-user@local` (or from `DELETED_USER_EMAIL` env var).

### 3. Create Super Admin User

You need at least one super-admin user to test deletion. Update an existing user or create one:

```bash
php artisan tinker
```

```php
$user = User::find(1); // or create new
$user->is_super_admin = true;
$user->save();
```

Or use a seeder:

```php
// database/seeders/SuperAdminSeeder.php
User::factory()->create([
    'email' => 'superadmin@example.com',
    'is_super_admin' => true,
]);
```

### 4. Add Config (Optional)

Add to `config/app.php` in the return array:

```php
'deleted_user_email' => env('DELETED_USER_EMAIL', 'deleted-user@local'),
```

### 5. Clear Caches

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

## Environment Variables

Add to `.env`:

```env
DELETED_USER_EMAIL=deleted-user@local
```

## Usage

### Soft Delete a User

1. Navigate to `/admin/users` (must be super-admin)
2. Click "حذف" (Delete) button next to a user
3. Confirm the action
4. User is soft-deleted, all posts reassigned to placeholder

### Restore a User

1. Navigate to `/admin/users?status=deleted`
2. Click "استعادة" (Restore) button
3. User is restored (posts remain with placeholder)

### Permanently Delete a User

1. Navigate to `/admin/users?status=deleted`
2. Click "حذف نهائي" (Force Delete)
3. Type "DELETE" to confirm
4. User is permanently removed from database

## Authorization

### Super Admin Check

The system uses `is_super_admin` boolean column. To adapt to Spatie roles:

**In `app/Models/User.php`:**

```php
public function isSuperAdmin(): bool
{
    // Option 1: Boolean (current)
    if ($this->is_super_admin) {
        return true;
    }
    
    // Option 2: Spatie role (uncomment to use)
    // return $this->hasRole('super-admin');
    
    return false;
}
```

**In `app/Http/Middleware/EnsureUserIsSuperAdmin.php`:**

```php
// Change from:
if (!$user || !$user->isSuperAdmin()) {

// To (if using Spatie):
if (!$user || !$user->hasRole('super-admin')) {
```

## Routes

All routes are protected with `auth` + `superadmin` middleware:

- `DELETE /admin/users/{user}` - Soft delete
- `POST /admin/users/{id}/restore` - Restore
- `DELETE /admin/users/{id}/force-delete` - Permanent delete

## Policies

`UserPolicy` provides:
- `delete()` - Can soft delete (super-admin only, not placeholder, not self)
- `restore()` - Can restore (super-admin only, must be trashed)
- `forceDelete()` - Can permanently delete (super-admin only, not placeholder, not self)

## Audit Logs

All actions are logged in `audit_logs` table:

```php
AuditLog::create([
    'actor_id' => auth()->id(),
    'target_user_id' => $user->id,
    'action' => 'soft_delete_user', // or 'restore_user', 'force_delete_user'
    'meta' => [
        'posts_reassigned' => 5,
        'ip' => request()->ip(),
        'timestamp' => now()->toIso8601String(),
    ],
]);
```

## Testing

Run tests:

```bash
php artisan test --filter SuperAdminDeleteUserTest
```

Tests verify:
- ✅ Only super-admin can delete
- ✅ Posts are reassigned to placeholder
- ✅ Audit log created
- ✅ Cannot delete placeholder
- ✅ Restore works

## Important Notes

### Post Reassignment

- Posts are **reassigned** (not deleted) when user is soft-deleted
- Posts **remain with placeholder** when user is restored
- To restore posts ownership, you'd need to implement `post_owner_history` table (see optional section below)

### Placeholder Protection

- Placeholder account cannot be deleted (protected in policy)
- Placeholder is automatically restored if soft-deleted
- Placeholder email: `deleted-user@local` (or from `DELETED_USER_EMAIL` env)

### Permanent Delete

- **WARNING**: Force delete is irreversible
- Requires typing "DELETE" to confirm
- Posts/media remain with placeholder (they were reassigned during soft delete)

## Optional: Post Owner History

To support restoring posts to original owner, create:

```bash
php artisan make:migration create_post_owner_history_table
```

Migration:

```php
Schema::create('post_owner_history', function (Blueprint $table) {
    $table->id();
    $table->foreignId('post_id')->constrained()->cascadeOnDelete();
    $table->foreignId('original_user_id')->constrained('users')->nullOnDelete();
    $table->foreignId('reassigned_to_user_id')->constrained('users')->nullOnDelete();
    $table->timestamp('reassigned_at');
    $table->timestamps();
});
```

Then before reassigning in controller:

```php
// Store history
foreach ($user->posts as $post) {
    PostOwnerHistory::create([
        'post_id' => $post->id,
        'original_user_id' => $user->id,
        'reassigned_to_user_id' => $placeholder->id,
        'reassigned_at' => now(),
    ]);
}
```

## Troubleshooting

### "Deleted placeholder user missing"

Run: `php artisan db:seed --class=DeletedUserSeeder`

### "403 Forbidden - Super Admin only"

Ensure user has `is_super_admin = true` in database.

### Posts not reassigning

Check that `Post` model has `user_id` foreign key. If using different column name, update controller.

## Files Modified/Created

**Migrations:**
- `database/migrations/2025_11_22_000001_add_softdelete_and_superadmin_to_users.php`
- `database/migrations/2025_11_22_000002_create_audit_logs_table.php`

**Models:**
- `app/Models/User.php` (added SoftDeletes, isSuperAdmin method)
- `app/Models/AuditLog.php` (new)

**Controllers:**
- `app/Http/Controllers/Admin/UserController.php` (added destroy, restore, forceDelete)

**Middleware:**
- `app/Http/Middleware/EnsureUserIsSuperAdmin.php` (new)
- `bootstrap/app.php` (registered middleware)

**Policies:**
- `app/Policies/UserPolicy.php` (new)
- `app/Providers/AuthServiceProvider.php` (registered policy)

**Seeders:**
- `database/seeders/DeletedUserSeeder.php` (new)

**Routes:**
- `routes/web.php` (added superadmin routes)

**Views:**
- `resources/views/admin/users/index.blade.php` (added delete/restore buttons, status filter)

**Tests:**
- `tests/Feature/SuperAdminDeleteUserTest.php` (new)

## Artisan Commands Summary

```bash
# Run migrations
php artisan migrate

# Seed placeholder user
php artisan db:seed --class=DeletedUserSeeder

# Clear caches
php artisan route:clear
php artisan config:clear
php artisan cache:clear

# Run tests
php artisan test --filter SuperAdminDeleteUserTest
```

## Support for Spatie Roles

If your project uses Spatie Permission and you prefer roles over boolean:

1. Create `super-admin` role: `php artisan permission:create-role super-admin`
2. Assign to users: `$user->assignRole('super-admin')`
3. Update `User::isSuperAdmin()` to use `hasRole('super-admin')`
4. Update middleware to check role instead of boolean

The code is designed to be easily adaptable to either approach.

