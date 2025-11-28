# Role Rename Summary: Editor → Moderator

## Overview
Renamed the `editor` role to `moderator` (مشرف) to better reflect its purpose as a content moderator, while maintaining `admin` role as Super Admin (مدير النظام). Also added protection for the "deleted user" placeholder account.

---

## Changes Made

### 1. Database Migration
**File:** `database/migrations/2025_11_24_100000_rename_editor_to_moderator.php`

- **Up Migration:**
  - Finds existing `editor` role
  - Renames it to `moderator` (preserves all user assignments)
  - Clears permission cache
  - Logs count of affected users

- **Down Migration:**
  - Renames `moderator` back to `editor`
  - Fully reversible

**Why:** Safe, atomic rename that preserves all existing user-role relationships.

---

### 2. Seeder Updates
**File:** `database/seeders/RolesAndPermissionsSeeder.php`

**Changes:**
```diff
- $editorRole = Role::firstOrCreate(['name' => 'editor'], ['guard_name' => 'web']);
+ $moderatorRole = Role::firstOrCreate(['name' => 'moderator'], ['guard_name' => 'web']);
```

**Why:** Ensures fresh installations create `moderator` role instead of `editor`.

---

### 3. Authorization Gates
**File:** `app/Providers/AuthServiceProvider.php`

**Changes:**
```diff
- Gate::define('manage-content', fn (User $user) => $user->isEditor() || ...);
+ Gate::define('manage-content', fn (User $user) => $user->hasRole('moderator') || ...);
```

**Why:** Updates authorization logic to check for `moderator` role.

---

### 4. User Model
**File:** `app/Models/User.php`

**Changes:**
```php
// New primary method
public function isModerator(): bool
{
    return $this->hasRole('moderator') || $this->role === UserRole::EDITOR;
}

// Backward compatibility alias
public function isEditor(): bool
{
    return $this->isModerator();
}
```

**Why:** Provides new `isModerator()` method while maintaining backward compatibility.

---

### 5. Policy Updates
**Files:**
- `app/Policies/PostPolicy.php`
- `app/Policies/CategoryPolicy.php`
- `app/Policies/TagPolicy.php`
- `app/Policies/MediaPolicy.php`

**Changes:**
```diff
- return $user->isAdmin() || $user->isEditor();
+ return $user->isAdmin() || $user->isModerator();
```

**Why:** All content policies now check for `moderator` role.

---

### 6. User Controller
**File:** `app/Http/Controllers/Admin/UserController.php`

**Changes:**
1. **Role Assignment:**
```diff
- $user->assignRole('editor');
+ $user->assignRole('moderator');
```

2. **Deleted User Protection:**
```php
// Added check before deletion
if ($user->isDeletedUserPlaceholder()) {
    return redirect()->back()
        ->with('error', 'لا يمكن حذف حساب المستخدم المحذوف الاحتياطي.');
}
```

**Why:** 
- New users get `moderator` role by default
- Prevents accidental deletion of placeholder account

---

### 7. Blade Views
**Files:**
- `resources/views/admin/users/index.blade.php`
- `resources/views/admin/users/create.blade.php`

**Changes:**
```diff
- {{ $role->name === 'editor' ? 'محرر' : ... }}
+ {{ $role->name === 'moderator' ? 'مشرف' : ... }}

- <span>محرر</span>
+ <span>مشرف</span>

- <span>مشرف</span> (for admin role)
+ <span>مدير النظام</span> (for admin role)
```

**Why:** UI now displays correct Arabic role names.

---

### 8. Tests
**File:** `tests/Feature/RoleBasedAccessControlTest.php`

**Changes:**
- Renamed all `editor` references to `moderator`
- Updated test method names: `editor_cannot_access_*` → `moderator_cannot_access_*`
- Added test for deleted user protection
- Updated role existence assertions

**Why:** Tests now verify `moderator` role behavior and deleted user protection.

---

## Role Structure (Final)

### مدير النظام (Super Admin)
- **Role Name:** `admin`
- **Database Flag:** `is_super_admin = true`
- **Permissions:**
  - ✅ Manage users (create, edit, delete, promote, demote)
  - ✅ Manage all content (posts, categories, tags, media)
  - ✅ Access all admin areas
  - ✅ Site settings (if implemented)

### مشرف (Moderator)
- **Role Name:** `moderator`
- **Database Flag:** `is_admin = true` (for admin panel access)
- **Permissions:**
  - ❌ Cannot manage users
  - ✅ Manage content (posts, categories, tags, media)
  - ✅ Create, edit, delete, publish posts
  - ❌ Cannot access user management
  - ❌ Cannot access site settings

---

## Protected Accounts

### 1. Super Admin (ID: 1)
- **Email:** `admin@example.com`
- **Protection:** Cannot be deleted or demoted

### 2. Deleted User Placeholder
- **Email:** `deleted-user@local` (configurable)
- **Protection:** Cannot be deleted
- **Purpose:** Receives posts from deleted users

---

## Verification Steps

### ✅ Database
```sql
-- Should return only 'admin' and 'moderator'
SELECT name FROM roles;

-- Count users with moderator role
SELECT COUNT(*) FROM model_has_roles 
WHERE role_id = (SELECT id FROM roles WHERE name = 'moderator');
```

### ✅ Code
```bash
# Search for any remaining 'editor' references
grep -r "hasRole('editor')" app/
grep -r "isEditor()" app/ --exclude-dir=vendor
grep -r "'editor'" resources/views/
```

### ✅ UI
1. Login as Super Admin → Should see "مدير النظام" badge
2. Login as Moderator → Should see "مشرف" badge
3. Moderator should NOT see "المستخدمين" menu item
4. Try to delete deleted user placeholder → Should fail with error

---

## Rollback Instructions

### Quick Rollback
```bash
php artisan migrate:rollback --step=1
php artisan permission:cache-reset
php artisan cache:clear
```

This will rename `moderator` back to `editor`.

---

## Git Diff Summary

### Files Modified (11)
1. `database/migrations/2025_11_24_100000_rename_editor_to_moderator.php` (NEW)
2. `database/seeders/RolesAndPermissionsSeeder.php`
3. `app/Providers/AuthServiceProvider.php`
4. `app/Models/User.php`
5. `app/Http/Controllers/Admin/UserController.php`
6. `app/Policies/PostPolicy.php`
7. `app/Policies/CategoryPolicy.php`
8. `app/Policies/TagPolicy.php`
9. `app/Policies/MediaPolicy.php`
10. `resources/views/admin/users/index.blade.php`
11. `resources/views/admin/users/create.blade.php`
12. `tests/Feature/RoleBasedAccessControlTest.php`

### Files Created (2)
1. `ROLE_RENAME_RUNBOOK.md`
2. `ROLE_RENAME_SUMMARY.md`

---

## Impact Assessment

### ✅ Safe Changes
- Role rename is atomic and reversible
- No user data deleted or lost
- All role assignments preserved
- Backward compatibility maintained via `isEditor()` alias

### ⚠️ Breaking Changes
- None - the `isEditor()` method still works as an alias

### 📝 Manual Steps Required
1. Run migration: `php artisan migrate`
2. Clear caches: `php artisan permission:cache-reset`
3. Test user access levels
4. Verify UI displays correct role names

---

## Performance Impact
- **Minimal:** Single UPDATE query to rename role
- **No N+1 queries:** Uses direct DB update
- **Cache cleared:** Permission cache reset required

---

## Security Improvements
1. ✅ Deleted user placeholder now protected from deletion
2. ✅ Explicit role-based authorization gates
3. ✅ Policy-level protection for all content types
4. ✅ UI elements hidden based on permissions

---

## Future Considerations

### Potential Enhancements
1. Add `display_name` column to `roles` table for localized names
2. Create role management UI for Super Admin
3. Add permission-level granularity (e.g., `publish-posts`, `delete-posts`)
4. Implement role-based dashboard widgets

### Migration Path
If you want to add more roles in the future:
1. Create new role via seeder
2. Update gates in `AuthServiceProvider`
3. Update policies to include new role
4. Update UI to display new role
5. Write tests for new role behavior

---

## Questions & Answers

**Q: Will existing users lose their access?**
A: No. The migration renames the role, so all user assignments are preserved.

**Q: Can I rollback after deployment?**
A: Yes. Run `php artisan migrate:rollback --step=1` to revert.

**Q: Do I need to update any API endpoints?**
A: Only if your API returns role names. Update serializers/transformers to return `moderator` instead of `editor`.

**Q: What about scheduled jobs or queued jobs?**
A: The `isEditor()` alias ensures backward compatibility. No changes needed.

---

**Last Updated:** 2025-11-24
**Version:** 1.0
**Status:** ✅ Ready for Deployment

