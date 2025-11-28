# Role Simplification Runbook

## Overview
This runbook documents the removal of the "Viewer/User" role and simplification to a two-role system:
- **Super Admin** (مدير النظام) - Full rights including user management
- **Editor** (محرر) - Content management only, cannot access user management

## Pre-Migration Checklist

### 1. Backup Database
```bash
# For MySQL/MariaDB
mysqldump -u username -p database_name > backup_before_role_simplification_$(date +%Y%m%d_%H%M%S).sql

# For PostgreSQL
pg_dump -U username database_name > backup_before_role_simplification_$(date +%Y%m%d_%H%M%S).sql

# For SQLite
cp database/database.sqlite database/database.sqlite.backup_$(date +%Y%m%d_%H%M%S)
```

### 2. Verify Current State
```bash
# Check existing roles
php artisan tinker
>>> \Spatie\Permission\Models\Role::pluck('name');

# Count users with 'user' role
>>> DB::table('model_has_roles')
      ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
      ->where('roles.name', 'user')
      ->count();
```

## Migration Steps (Staging Environment)

### Step 1: Pull Latest Code
```bash
git pull origin main
```

### Step 2: Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### Step 3: Run Migration (with --step for safety)
```bash
php artisan migrate --step
```

**Expected Output:**
```
Migrating: 2025_11_24_000000_remove_viewer_role_and_reassign_users
Found X user(s) with 'user' role.
Reassigned X user(s) to 'editor' role.
Deleted 'user' role successfully.
Migrated:  2025_11_24_000000_remove_viewer_role_and_reassign_users
```

### Step 4: Reseed Roles (Optional - only if roles table is empty)
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### Step 5: Clear Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Step 6: Run Tests
```bash
php artisan test --filter=RoleBasedAccessControlTest
```

**Expected:** All tests should pass.

### Step 7: Manual Verification

#### A. Super Admin Verification
1. Log in as Super Admin
2. Verify you can see "المستخدمين" (Users) in sidebar
3. Navigate to Users page (`/admin/users`)
4. Create a new user and assign "محرر" (Editor) role
5. Verify the new user appears with "محرر" badge

#### B. Editor Verification
1. Log in as Editor
2. Verify "المستخدمين" (Users) does NOT appear in sidebar
3. Try to access `/admin/users` directly → Should get 403 Forbidden
4. Verify you CAN access:
   - Posts (`/admin/posts`)
   - Categories (`/admin/categories`)
   - Tags (`/admin/tags`)
   - Media (`/admin/media`)

#### C. Role Badge Verification
1. Go to Users list as Super Admin
2. Verify role badges display correctly:
   - Purple badge "مدير النظام" for Super Admins
   - Blue badge "محرر" for Editors
   - NO "مستخدم" or "Viewer" badges

## Production Deployment

### Prerequisites
- ✅ All tests passing in staging
- ✅ Manual verification completed
- ✅ Database backup created
- ✅ Rollback plan reviewed

### Deployment Steps
```bash
# 1. Enable maintenance mode
php artisan down --message="System upgrade in progress" --retry=60

# 2. Pull latest code
git pull origin main

# 3. Install dependencies
composer install --no-dev --optimize-autoloader

# 4. Run migration
php artisan migrate --force

# 5. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 6. Optimize
php artisan optimize

# 7. Disable maintenance mode
php artisan up
```

## Rollback Procedure

### If Migration Fails
```bash
# Rollback the specific migration
php artisan migrate:rollback --step=1

# Restore from backup
mysql -u username -p database_name < backup_file.sql
# OR for SQLite
cp database/database.sqlite.backup_YYYYMMDD_HHMMSS database/database.sqlite

# Clear caches
php artisan cache:clear
php artisan config:clear
```

### If Issues Discovered After Deployment
```bash
# 1. Enable maintenance mode
php artisan down

# 2. Rollback migration
php artisan migrate:rollback --step=1

# 3. Restore previous code
git checkout previous_commit_hash

# 4. Clear caches
php artisan cache:clear
php artisan config:clear

# 5. Bring site back up
php artisan up
```

## Post-Migration Verification

### 1. Check Database State
```bash
php artisan tinker
>>> \Spatie\Permission\Models\Role::pluck('name');
# Expected: ["admin", "editor"]

>>> \Spatie\Permission\Models\Role::where('name', 'user')->exists();
# Expected: false
```

### 2. Check User Assignments
```bash
# Count users by role
>>> DB::table('model_has_roles')
      ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
      ->select('roles.name', DB::raw('count(*) as count'))
      ->groupBy('roles.name')
      ->get();
```

### 3. Verify Access Control
- Super Admin can access all admin routes
- Editor can access content routes but NOT user management
- No 500 errors in logs

## Troubleshooting

### Issue: "Role not found" errors
**Solution:**
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan cache:clear
```

### Issue: Users can't access admin panel
**Solution:**
```bash
# Check user roles
php artisan tinker
>>> User::find(USER_ID)->roles;

# Reassign role if needed
>>> $user = User::find(USER_ID);
>>> $user->assignRole('editor'); // or 'admin'
```

### Issue: Permission denied errors
**Solution:**
```bash
# Clear permission cache
php artisan permission:cache-reset
php artisan cache:clear
```

## Files Modified

### Database & Seeders
- `database/migrations/2025_11_24_000000_remove_viewer_role_and_reassign_users.php` (NEW)
- `database/seeders/RolesAndPermissionsSeeder.php` (MODIFIED)

### Authorization
- `app/Providers/AuthServiceProvider.php` (MODIFIED)
- `app/Policies/UserPolicy.php` (NO CHANGES - already correct)

### Controllers
- `app/Http/Controllers/Admin/UserController.php` (MODIFIED)

### Views
- `resources/views/admin/users/index.blade.php` (MODIFIED)
- `resources/views/admin/users/create.blade.php` (MODIFIED)
- `resources/views/layouts/admin.blade.php` (MODIFIED)

### Tests
- `tests/Feature/RoleBasedAccessControlTest.php` (NEW)

## Summary of Changes

### What Changed
1. **Removed "user" role** from Spatie Permission roles table
2. **Reassigned all users** with "user" role to "editor" role
3. **Updated UI labels** to show "محرر" instead of "مستخدم"
4. **Restricted user management** to Super Admin only via `@can('manage-users')` gate
5. **Updated user creation** to default new users to Editor role
6. **Added Super Admin checkbox** in user create form (only visible to Super Admins)

### What Stayed the Same
- Super Admin functionality unchanged
- Content management permissions unchanged
- Profile page unchanged
- Authentication flow unchanged
- Database structure unchanged (only role assignments)

## Support Contacts
- Technical Lead: [Your Name]
- Database Admin: [DBA Name]
- DevOps: [DevOps Contact]

## Revision History
- 2025-11-24: Initial runbook created

