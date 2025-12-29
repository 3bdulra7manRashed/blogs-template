# Role Rename Runbook: Editor → Moderator
## مدير النظام (Super Admin) & مشرف (Moderator)

This runbook provides step-by-step instructions for safely renaming the `editor` role to `moderator` and protecting the "deleted user" placeholder account.

---

## Pre-Deployment Checklist

### 1. Backup Database
**CRITICAL: Always backup before making role changes**

```bash
# For MySQL
mysqldump -u your_username -p your_database_name > backup_before_role_rename_$(date +%Y%m%d_%H%M%S).sql

# For SQLite (if using SQLite)
cp database/database.sqlite database/database.sqlite.backup_$(date +%Y%m%d_%H%M%S)
```

### 2. Verify Current State
```bash
# Check current roles in database
php artisan tinker
>>> \Spatie\Permission\Models\Role::pluck('name');
# Should show: ['admin', 'editor']

# Count users with editor role
>>> DB::table('model_has_roles')->where('role_id', \Spatie\Permission\Models\Role::where('name', 'editor')->first()->id)->count();
```

### 3. Test on Staging First
**DO NOT apply directly to production without testing on staging environment**

---

## Deployment Steps

### Step 1: Pull Latest Code
```bash
git pull origin main
# Or your branch name
```

### Step 2: Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Step 3: Run Migration
```bash
php artisan migrate
```

**Expected Output:**
```
Migrating: 2025_11_24_100000_rename_editor_to_moderator
Found X user(s) with 'editor' role.
Renamed 'editor' role to 'moderator'. X user(s) now have 'moderator' role.
Migrated:  2025_11_24_100000_rename_editor_to_moderator
```

### Step 4: Run Seeder (Optional - only if roles are missing)
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### Step 5: Clear Permission Cache
```bash
php artisan permission:cache-reset
php artisan cache:clear
```

### Step 6: Verify Changes
```bash
php artisan tinker
>>> \Spatie\Permission\Models\Role::pluck('name');
# Should show: ['admin', 'moderator']

# Verify users were reassigned
>>> DB::table('model_has_roles')->where('role_id', \Spatie\Permission\Models\Role::where('name', 'moderator')->first()->id)->count();
# Should match the count from pre-deployment check
```

### Step 7: Run Tests
```bash
php artisan test --filter=RoleBasedAccessControlTest
```

**Expected: All tests pass**

### Step 8: Manual UI Verification
1. Login as Super Admin
   - ✅ Can access "المستخدمين" (Users)
   - ✅ Can create/edit/delete users
   - ✅ Can manage all content

2. Login as Moderator (مشرف)
   - ✅ Can access Dashboard, Posts, Media, Tags, Categories
   - ❌ Cannot access "المستخدمين" (Users) - should be hidden
   - ✅ Can create/edit/delete posts

3. Verify "Deleted User" Protection
   - Login as Super Admin
   - Try to delete the "deleted-user@local" account
   - ✅ Should show error: "لا يمكن حذف حساب المستخدم المحذوف الاحتياطي."

---

## Rollback Procedure

### If Something Goes Wrong

#### Option 1: Rollback Migration (Preferred)
```bash
php artisan migrate:rollback --step=1
php artisan permission:cache-reset
php artisan cache:clear
```

**This will:**
- Rename 'moderator' back to 'editor'
- Restore previous state

#### Option 2: Restore from Backup
```bash
# For MySQL
mysql -u your_username -p your_database_name < backup_before_role_rename_YYYYMMDD_HHMMSS.sql

# For SQLite
cp database/database.sqlite.backup_YYYYMMDD_HHMMSS database/database.sqlite
```

After restoring:
```bash
php artisan permission:cache-reset
php artisan cache:clear
php artisan config:clear
```

---

## Post-Deployment Verification

### 1. Check Logs
```bash
tail -f storage/logs/laravel.log
# Look for any errors related to roles or permissions
```

### 2. Verify User Assignments
```bash
php artisan tinker
>>> $moderators = \App\Models\User::role('moderator')->get();
>>> $moderators->pluck('name', 'email');
# Verify all expected users have moderator role
```

### 3. Test Key Workflows
- [ ] Super Admin can create new moderator account
- [ ] Moderator can create/publish posts
- [ ] Moderator cannot access user management
- [ ] Deleted user placeholder is protected

---

## Troubleshooting

### Issue: "Role moderator does not exist"
**Solution:**
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan permission:cache-reset
```

### Issue: Users lost their roles
**Solution:**
```bash
# Check migration output - it should have logged reassignments
# If needed, manually reassign:
php artisan tinker
>>> $user = \App\Models\User::find(USER_ID);
>>> $user->assignRole('moderator');
```

### Issue: Permission denied errors
**Solution:**
```bash
php artisan permission:cache-reset
php artisan cache:clear
php artisan config:clear
```

---

## Safety Notes

1. **Never delete user accounts** - This migration only renames roles
2. **All changes are reversible** - Use `migrate:rollback` if needed
3. **Test on staging first** - Never apply directly to production
4. **Monitor logs** - Check for errors after deployment
5. **Backup is mandatory** - Always backup before role changes

---

## Summary of Changes

### Database
- ✅ Renamed `editor` role to `moderator` in `roles` table
- ✅ All user role assignments automatically updated (via role_id)
- ✅ No user accounts deleted or modified

### Code
- ✅ Updated all policy files to use `isModerator()` instead of `isEditor()`
- ✅ Updated AuthServiceProvider gates
- ✅ Updated UserController role assignment logic
- ✅ Updated all Blade views with Arabic role names
- ✅ Added protection for deleted user placeholder

### UI
- ✅ Role badges now show "مشرف" instead of "محرر"
- ✅ Super Admin badge shows "مدير النظام"
- ✅ User creation form updated with correct role names

---

## Contact

If you encounter any issues during deployment:
1. Check the troubleshooting section above
2. Review `storage/logs/laravel.log`
3. Rollback if necessary
4. Report the issue with full error details

---

**Deployment Date:** _________________

**Deployed By:** _________________

**Rollback Required:** ☐ Yes  ☐ No

**Notes:** _________________

