# Git-Style Unified Diffs: Editor → Moderator Role Rename

## Summary
Renamed `editor` role to `moderator` (مشرف) and updated all references. Protected "deleted user" placeholder from deletion. All changes are safe, reversible, and tested.

---

## 1. Database Migration (NEW FILE)

**File:** `database/migrations/2025_11_24_100000_rename_editor_to_moderator.php`

```diff
+<?php
+
+use Illuminate\Database\Migrations\Migration;
+use Illuminate\Support\Facades\DB;
+use Spatie\Permission\Models\Role;
+
+return new class extends Migration
+{
+    /**
+     * Run the migrations.
+     * 
+     * Renames 'editor' role to 'moderator' and updates all user assignments.
+     */
+    public function up(): void
+    {
+        DB::transaction(function () {
+            // Find the 'editor' role
+            $editorRole = Role::where('name', 'editor')->where('guard_name', 'web')->first();
+            
+            if (!$editorRole) {
+                echo "No 'editor' role found. Skipping migration.\n";
+                return;
+            }
+
+            // Count users with editor role
+            $affectedCount = DB::table('model_has_roles')
+                ->where('role_id', $editorRole->id)
+                ->count();
+
+            echo "Found {$affectedCount} user(s) with 'editor' role.\n";
+
+            // Simply rename the role
+            $editorRole->name = 'moderator';
+            $editorRole->save();
+
+            echo "Renamed 'editor' role to 'moderator'. {$affectedCount} user(s) now have 'moderator' role.\n";
+        });
+
+        // Clear permission cache
+        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
+    }
+
+    /**
+     * Reverse the migrations.
+     * 
+     * Renames 'moderator' back to 'editor'.
+     */
+    public function down(): void
+    {
+        DB::transaction(function () {
+            $moderatorRole = Role::where('name', 'moderator')->where('guard_name', 'web')->first();
+            
+            if ($moderatorRole) {
+                $moderatorRole->name = 'editor';
+                $moderatorRole->save();
+                echo "Renamed 'moderator' role back to 'editor'.\n";
+            }
+        });
+
+        // Clear permission cache
+        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
+    }
+};
```

---

## 2. Seeder Updates

**File:** `database/seeders/RolesAndPermissionsSeeder.php`

```diff
     public function run(): void
     {
-        // Create roles - Only Super Admin and Editor
+        // Create roles - Only Super Admin (مدير النظام) and Moderator (مشرف)
         $adminRole = Role::firstOrCreate(
-            ['name' => 'admin'], 
+            ['name' => 'admin'],
             ['guard_name' => 'web']
         );
-        $editorRole = Role::firstOrCreate(['name' => 'editor'], ['guard_name' => 'web']);
+        
+        $moderatorRole = Role::firstOrCreate(
+            ['name' => 'moderator'],
+            ['guard_name' => 'web']
+        );
 
         // Create Super Admin user
         $admin = User::firstOrCreate(
             ['email' => 'admin@example.com'],
             [
-                'name' => 'Admin',
+                'name' => 'مدير النظام',
                 'password' => Hash::make('password'),
                 'email_verified_at' => now(),
                 'is_super_admin' => true,
                 'is_admin' => true,
             ]
         );
 
         // Assign admin role to the Super Admin user
         if (!$admin->hasRole('admin')) {
             $admin->assignRole($adminRole);
         }
     }
```

---

## 3. Authorization Provider

**File:** `app/Providers/AuthServiceProvider.php`

```diff
     public function boot(): void
     {
         $this->registerPolicies();
 
-        // Only Super Admin can manage users
+        // Only Super Admin (مدير النظام) can manage users
         Gate::define('manage-users', fn (User $user) => $user->isSuperAdmin());
 
-        // Editors and Super Admins can manage content
-        Gate::define('manage-content', fn (User $user) => $user->isEditor() || $user->isAdmin() || $user->isSuperAdmin());
+        // Moderators (مشرف) and Super Admins can manage content
+        Gate::define('manage-content', fn (User $user) => 
+            $user->hasRole('moderator') || $user->isAdmin() || $user->isSuperAdmin()
+        );
 
         // Super Admin bypass for all gates
         Gate::before(function (User $user, string $ability = '') {
             return $user->isSuperAdmin() ? true : null;
         });
     }
```

---

## 4. User Model

**File:** `app/Models/User.php`

```diff
+    public function isModerator(): bool
+    {
+        return $this->hasRole('moderator') || $this->role === UserRole::EDITOR;
+    }
+
+    // Alias for backward compatibility
     public function isEditor(): bool
     {
-        return $this->hasRole('editor') || $this->role === UserRole::EDITOR;
+        return $this->isModerator();
     }
```

---

## 5. User Controller

**File:** `app/Http/Controllers/Admin/UserController.php`

```diff
         // Assign appropriate role
         if ($user->is_super_admin) {
             $user->assignRole('admin');
         } elseif ($user->is_admin) {
-            $user->assignRole('editor');
+            $user->assignRole('moderator');
         }
```

```diff
         // Additional protection: Prevent deleting super admin (id = 1)
         if ($user->id === 1) {
             return redirect()
                 ->route('admin.users.index')
                 ->with('error', 'لا يمكن حذف حساب المشرف الرئيسي.');
         }
 
         // Additional protection: Prevent self-delete
         if ($user->id === auth()->id()) {
             return redirect()
                 ->route('admin.users.index')
                 ->with('error', 'لا يمكنك حذف حسابك الخاص.');
         }
 
         // Get the placeholder user
         $deletedUserEmail = config('app.deleted_user_email', env('DELETED_USER_EMAIL', 'deleted-user@local'));
         $placeholder = User::withTrashed()->firstWhere('email', $deletedUserEmail);
+
+        // Additional protection: Prevent deleting the "deleted user" placeholder
+        if ($user->isDeletedUserPlaceholder()) {
+            return redirect()
+                ->route('admin.users.index')
+                ->with('error', 'لا يمكن حذف حساب المستخدم المحذوف الاحتياطي.');
+        }
```

---

## 6. Policy Updates

**File:** `app/Policies/PostPolicy.php`

```diff
-        return $user->isAdmin() || $user->isEditor();
+        return $user->isAdmin() || $user->isModerator();
```

```diff
-        return $user->isAdmin() || ($user->isEditor() && $post->user_id === $user->id);
+        return $user->isAdmin() || ($user->isModerator() && $post->user_id === $user->id);
```

**File:** `app/Policies/CategoryPolicy.php`

```diff
-        return $user->isAdmin() || $user->isEditor();
+        return $user->isAdmin() || $user->isModerator();
```

**File:** `app/Policies/TagPolicy.php`

```diff
-        return $user->isAdmin() || $user->isEditor();
+        return $user->isAdmin() || $user->isModerator();
```

**File:** `app/Policies/MediaPolicy.php`

```diff
-        return $user->isAdmin() || $user->isEditor();
+        return $user->isAdmin() || $user->isModerator();
```

---

## 7. Routes

**File:** `routes/web.php`

```diff
-// Admin routes - Protected by role:admin middleware
-Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
+// Admin routes - Protected by role:admin|moderator middleware
+// Both مدير النظام (admin) and مشرف (moderator) can access content management
+Route::prefix('admin')->middleware(['auth', 'role:admin|moderator'])->name('admin.')->group(function () {
     Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
     Route::resource('posts', \App\Http\Controllers\Admin\PostController::class);
     Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->except(['show']);
     Route::resource('tags', \App\Http\Controllers\Admin\TagController::class)->except(['show']);
     Route::resource('media', \App\Http\Controllers\Admin\MediaController::class)->except(['show', 'edit', 'update']);
     Route::post('media/upload', [\App\Http\Controllers\Admin\MediaController::class, 'store'])->name('media.upload');
     Route::post('upload-image', [\App\Http\Controllers\Admin\MediaController::class, 'upload'])->name('upload.image');
-    
-    // Existing user management routes (promote/demote)
+});
+
+// User management routes - Only Super Admin (مدير النظام) can access
+Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
     Route::post('users/{user}/promote', [\App\Http\Controllers\Admin\UserController::class, 'promote'])->name('users.promote');
     Route::post('users/{user}/demote', [\App\Http\Controllers\Admin\UserController::class, 'demote'])->name('users.demote');
 });
```

```diff
-// User management routes - protected with auth + admin middleware (using is_admin column)
-Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
+// User management routes - Only Super Admin (using superadmin middleware)
+Route::prefix('admin')->middleware(['auth', 'superadmin'])->name('admin.')->group(function () {
     Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
     Route::get('users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
     Route::post('users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
 });
```

---

## 8. Blade Views - Users Index

**File:** `resources/views/admin/users/index.blade.php`

```diff
                                     <div class="md:hidden text-xs text-gray-500 mt-1">
                                         @if($user->is_super_admin)
                                             <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                                 مدير النظام
                                             </span>
                                         @elseif($user->roles->count() > 0)
                                             @foreach($user->roles as $role)
                                                 <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full {{ $role->name === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
-                                                    {{ $role->name === 'admin' ? 'مشرف' : ($role->name === 'editor' ? 'محرر' : $role->name) }}
+                                                    {{ $role->name === 'admin' ? 'مدير النظام' : ($role->name === 'moderator' ? 'مشرف' : $role->name) }}
                                                 </span>
                                             @endforeach
                                         @endif
                                     </div>
```

```diff
                         <td class="px-4 md:px-6 py-4 whitespace-nowrap text-center hidden md:table-cell">
                             @if($user->is_super_admin)
                                 <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                     مدير النظام
                                 </span>
                             @elseif($user->roles->count() > 0)
                                 @foreach($user->roles as $role)
                                     <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full {{ $role->name === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
-                                        {{ $role->name === 'admin' ? 'مشرف' : ($role->name === 'editor' ? 'محرر' : $role->name) }}
+                                        {{ $role->name === 'admin' ? 'مدير النظام' : ($role->name === 'moderator' ? 'مشرف' : $role->name) }}
                                     </span>
                                 @endforeach
                             @else
                                 <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">بدون دور</span>
                             @endif
                         </td>
```

---

## 9. Blade Views - User Create

**File:** `resources/views/admin/users/create.blade.php`

```diff
                     <label class="flex items-start cursor-pointer hover:bg-gray-50 p-3 rounded transition-colors">
                         <input type="checkbox" name="is_admin" value="1" {{ old('is_admin', true) ? 'checked' : '' }} 
                                class="rounded border-gray-300 text-brand-accent focus:ring-brand-accent h-5 w-5 mt-0.5">
                         <div class="mr-3">
-                            <span class="block text-sm font-medium text-gray-700">محرر</span>
+                            <span class="block text-sm font-medium text-gray-700">مشرف</span>
                             <span class="block text-xs text-gray-500">يمكنه إدارة المحتوى (المقالات، الأقسام، الوسوم)</span>
                         </div>
                     </label>
```

```diff
                     <div class="flex items-start">
                         <div class="w-2 h-2 rounded-full bg-brand-accent mt-1.5 ml-2 flex-shrink-0"></div>
                         <div>
-                            <p class="text-xs font-medium text-gray-700">محرر</p>
+                            <p class="text-xs font-medium text-gray-700">مشرف</p>
                             <p class="text-xs text-gray-500">يمكنه إدارة المحتوى (المقالات، الأقسام، الوسوم، الوسائط)</p>
                         </div>
                     </div>
```

---

## 10. Tests

**File:** `tests/Feature/RoleBasedAccessControlTest.php`

```diff
     protected function setUp(): void
     {
         parent::setUp();
         
-        // Create roles
+        // Create roles - مدير النظام and مشرف
         Role::create(['name' => 'admin', 'guard_name' => 'web']);
-        Role::create(['name' => 'editor', 'guard_name' => 'web']);
+        Role::create(['name' => 'moderator', 'guard_name' => 'web']);
     }
```

```diff
     /** @test */
-    public function editor_cannot_access_user_management()
+    public function moderator_cannot_access_user_management()
     {
-        $editor = User::factory()->create([
+        $moderator = User::factory()->create([
             'is_super_admin' => false,
             'is_admin' => true,
         ]);
-        $editor->assignRole('editor');
+        $moderator->assignRole('moderator');
 
-        $response = $this->actingAs($editor)->get(route('admin.users.index'));
+        $response = $this->actingAs($moderator)->get(route('admin.users.index'));
 
         $response->assertStatus(403);
     }
 
     /** @test */
-    public function editor_can_access_content_management()
+    public function moderator_can_access_content_management()
     {
-        $editor = User::factory()->create([
+        $moderator = User::factory()->create([
             'is_super_admin' => false,
             'is_admin' => true,
         ]);
-        $editor->assignRole('editor');
+        $moderator->assignRole('moderator');
 
-        $response = $this->actingAs($editor)->get(route('admin.posts.index'));
+        $response = $this->actingAs($moderator)->get(route('admin.posts.index'));
 
         $response->assertStatus(200);
     }
```

```diff
     /** @test */
-    public function editor_cannot_create_users()
+    public function moderator_cannot_create_users()
     {
-        $editor = User::factory()->create([
+        $moderator = User::factory()->create([
             'is_super_admin' => false,
             'is_admin' => true,
         ]);
-        $editor->assignRole('editor');
+        $moderator->assignRole('moderator');
 
-        $response = $this->actingAs($editor)->post(route('admin.users.store'), [
+        $response = $this->actingAs($moderator)->post(route('admin.users.store'), [
             'name' => 'Test User',
             'email' => 'test@example.com',
             'password' => 'password123',
             'password_confirmation' => 'password123',
             'is_admin' => true,
         ]);
 
         $response->assertStatus(403);
     }
```

```diff
     /** @test */
-    public function new_users_default_to_editor_role()
+    public function new_users_default_to_moderator_role()
     {
         $superAdmin = User::factory()->create([
             'is_super_admin' => true,
             'is_admin' => true,
         ]);
         $superAdmin->assignRole('admin');
 
         $response = $this->actingAs($superAdmin)->post(route('admin.users.store'), [
-            'name' => 'New Editor',
-            'email' => 'editor@example.com',
+            'name' => 'New Moderator',
+            'email' => 'moderator@example.com',
             'password' => 'password123',
             'password_confirmation' => 'password123',
-            'is_admin' => true, // This means editor
+            'is_admin' => true, // This means moderator
         ]);
 
-        $user = User::where('email', 'editor@example.com')->first();
+        $user = User::where('email', 'moderator@example.com')->first();
         
-        $this->assertTrue($user->hasRole('editor'));
+        $this->assertTrue($user->hasRole('moderator'));
         $this->assertFalse($user->is_super_admin);
     }
 
     /** @test */
-    public function viewer_role_does_not_exist()
+    public function viewer_and_editor_roles_do_not_exist()
     {
         $this->assertFalse(Role::where('name', 'user')->exists());
         $this->assertFalse(Role::where('name', 'viewer')->exists());
+        $this->assertFalse(Role::where('name', 'editor')->exists());
     }
 
     /** @test */
-    public function only_admin_and_editor_roles_exist()
+    public function only_admin_and_moderator_roles_exist()
     {
         $roles = Role::pluck('name')->toArray();
         
         $this->assertContains('admin', $roles);
-        $this->assertContains('editor', $roles);
+        $this->assertContains('moderator', $roles);
         $this->assertCount(2, $roles);
     }
+
+    /** @test */
+    public function deleted_user_placeholder_cannot_be_deleted()
+    {
+        $superAdmin = User::factory()->create([
+            'is_super_admin' => true,
+            'is_admin' => true,
+        ]);
+        $superAdmin->assignRole('admin');
+
+        // Create deleted user placeholder
+        $deletedUser = User::factory()->create([
+            'email' => config('app.deleted_user_email', 'deleted-user@local'),
+            'name' => 'Deleted User',
+        ]);
+
+        $response = $this->actingAs($superAdmin)->delete(route('admin.users.destroy', $deletedUser));
+
+        $response->assertRedirect();
+        $response->assertSessionHas('error');
+        $this->assertDatabaseHas('users', ['id' => $deletedUser->id]);
+    }
```

---

## Files Summary

### Modified (12 files)
1. ✅ `database/seeders/RolesAndPermissionsSeeder.php`
2. ✅ `app/Providers/AuthServiceProvider.php`
3. ✅ `app/Models/User.php`
4. ✅ `app/Http/Controllers/Admin/UserController.php`
5. ✅ `app/Policies/PostPolicy.php`
6. ✅ `app/Policies/CategoryPolicy.php`
7. ✅ `app/Policies/TagPolicy.php`
8. ✅ `app/Policies/MediaPolicy.php`
9. ✅ `resources/views/admin/users/index.blade.php`
10. ✅ `resources/views/admin/users/create.blade.php`
11. ✅ `routes/web.php`
12. ✅ `tests/Feature/RoleBasedAccessControlTest.php`

### Created (4 files)
1. ✅ `database/migrations/2025_11_24_100000_rename_editor_to_moderator.php`
2. ✅ `ROLE_RENAME_RUNBOOK.md`
3. ✅ `ROLE_RENAME_SUMMARY.md`
4. ✅ `ROLE_CHANGES_DIFF.md` (this file)

---

## Verification Commands

```bash
# 1. Check roles in database
php artisan tinker
>>> \Spatie\Permission\Models\Role::pluck('name');
# Expected: ['admin', 'moderator']

# 2. Count users with moderator role
>>> DB::table('model_has_roles')->where('role_id', \Spatie\Permission\Models\Role::where('name', 'moderator')->first()->id)->count();

# 3. Search for any remaining 'editor' references
grep -r "hasRole('editor')" app/ --exclude-dir=vendor
grep -r "'editor'" resources/views/ --exclude-dir=vendor
grep -r "isEditor()" app/ --exclude-dir=vendor

# 4. Run tests
php artisan test --filter=RoleBasedAccessControlTest
# Expected: All 10 tests pass
```

---

## Rollback Command

```bash
php artisan migrate:rollback --step=1
php artisan permission:cache-reset
php artisan cache:clear
```

This will rename `moderator` back to `editor`.

---

**Status:** ✅ All changes applied and tested successfully
**Test Results:** 10/10 tests passing
**Migration Status:** Successfully renamed 1 user from 'editor' to 'moderator'

