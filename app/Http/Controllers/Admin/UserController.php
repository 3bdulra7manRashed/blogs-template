<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        Gate::authorize('manage-users');
        
        // Show all users including soft-deleted for super-admins
        $query = User::with('roles')->withTrashed();
        
        // Filter by status if requested
        if ($request->has('status')) {
            match ($request->status) {
                'deleted' => $query->onlyTrashed(),
                'active' => $query->whereNull('deleted_at'),
                default => null,
            };
        }
        
        $users = $query->latest('created_at')->paginate(15)->withQueryString();
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        Gate::authorize('manage-users');
        
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-users');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:8|confirmed',
            'is_admin' => 'sometimes|boolean',
            'is_super_admin' => 'sometimes|boolean',
        ]);

        // Only super admin can create other super admins
        if ($request->boolean('is_super_admin') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'فقط المشرف الرئيسي يمكنه إنشاء مشرفين رئيسيين آخرين');
        }

        if (empty($data['password'])) {
            $plainPassword = Str::random(12);
        } else {
            $plainPassword = $data['password'];
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($plainPassword),
            'is_admin' => $request->boolean('is_admin', true), // Default to editor (is_admin=true)
            'is_super_admin' => $request->boolean('is_super_admin', false),
            'email_verified_at' => now(),
        ]);

        // Assign appropriate role
        if ($user->is_super_admin) {
            $user->assignRole('admin');
        } elseif ($user->is_admin) {
            $user->assignRole('moderator');
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح.')
            ->with('plain_password', $plainPassword);
    }

    /**
     * Promote a user to admin role.
     */
    public function promote(User $user): RedirectResponse
    {
        // Assign the admin role using Spatie Permission
        $user->assignRole('admin');

        return redirect()
            ->back()
            ->with('success', 'تم ترقية المستخدم إلى مشرف بنجاح.');
    }

    /**
     * Remove admin role from a user.
     */
    public function demote(User $user): RedirectResponse
    {
        // Prevent demoting yourself
        if ($user->id === auth()->id()) {
            return redirect()
                ->back()
                ->with('error', 'لا يمكنك إزالة صلاحيات المشرف من حسابك الخاص.');
        }

        // Prevent demoting the root admin (admin@example.com)
        if ($user->email === 'admin@example.com') {
            return redirect()
                ->back()
                ->with('error', 'لا يمكن إزالة صلاحيات المشرف الرئيسي.');
        }

        // Remove the admin role
        $user->removeRole('admin');

        return redirect()
            ->back()
            ->with('success', 'تم إزالة صلاحيات المشرف من المستخدم بنجاح.');
    }

    /**
     * Soft delete a user and reassign their posts to the Deleted User placeholder.
     */
    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

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

        // Additional protection: Prevent deleting the "deleted user" placeholder
        if ($user->isDeletedUserPlaceholder()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'لا يمكن حذف حساب المستخدم المحذوف الاحتياطي.');
        }

        if (!$placeholder) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', "خطأ: حساب المستخدم المحذوف ($deletedUserEmail) غير موجود. يرجى تشغيل: php artisan db:seed --class=DeletedUserSeeder");
        }

        // Ensure placeholder is not soft-deleted
        if ($placeholder->trashed()) {
            $placeholder->restore();
        }

        // Compute counts BEFORE deletion/reassignment
        $postsCount = $user->posts()->count();
        $mediaCount = method_exists($user, 'media') ? $user->media()->count() : 0;

        DB::transaction(function () use ($user, $placeholder, $postsCount, $mediaCount) {
            // Reassign all posts to placeholder
            Post::where('user_id', $user->id)->update(['user_id' => $placeholder->id]);

            // Optionally reassign media (if exists)
            if (method_exists($user, 'media')) {
                $user->media()->update(['user_id' => $placeholder->id]);
            }

            // Soft delete the user
            $user->delete();

            // Create audit log entry
            AuditLog::create([
                'actor_id' => auth()->id(),
                'target_user_id' => $user->id,
                'action' => 'soft_delete_user',
                'meta' => [
                    'posts_reassigned' => $postsCount,
                    'media_reassigned' => $mediaCount,
                    'placeholder_user_id' => $placeholder->id,
                    'ip' => request()->ip(),
                    'timestamp' => now()->toIso8601String(),
                ],
            ]);
        });

        // Build message with newlines (JS will convert to <br>)
        if ($postsCount === 0) {
            $message = "تم حذف المستخدم بنجاح.";
        } else {
            $articleWord = $postsCount > 1 ? 'مقالات' : 'مقال';
            $message = "تم حذف المستخدم بنجاح.\nتم نقل {$postsCount} {$articleWord} إلى حساب المستخدم المحذوف.";
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', $message);
    }

    /**
     * Restore a soft-deleted user.
     * Note: Posts are NOT automatically moved back. They remain with the Deleted User placeholder.
     */
    public function restore($id): RedirectResponse
    {
        $user = User::withTrashed()->findOrFail($id);
        Gate::authorize('restore', $user);

        $user->restore();

        AuditLog::create([
            'actor_id' => auth()->id(),
            'target_user_id' => $user->id,
            'action' => 'restore_user',
            'meta' => [
                'ip' => request()->ip(),
                'timestamp' => now()->toIso8601String(),
                'note' => 'Posts remain with Deleted User placeholder',
            ],
        ]);

        // Build message with newlines (JS will convert to <br>)
        $message = "تم استعادة المستخدم بنجاح.\nملاحظة: المقالات تبقى مع حساب المستخدم المحذوف.";

        return redirect()
            ->route('admin.users.index')
            ->with('success', $message);
    }

    /**
     * Permanently delete a user (force delete).
     * WARNING: This is irreversible and should be used with extreme caution.
     */
    public function forceDelete($id): RedirectResponse
    {
        $user = User::withTrashed()->findOrFail($id);
        Gate::authorize('forceDelete', $user);

        // Additional protection: Prevent force deleting super admin (id = 1)
        if ($user->id === 1) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'لا يمكن حذف حساب المشرف الرئيسي نهائياً.');
        }

        // Additional protection: Prevent self-delete
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'لا يمكنك حذف حسابك الخاص نهائياً.');
        }

        // Compute counts or meta needed for audit before we delete
        $postsCount = Post::where('user_id', $user->id)->count();
        $mediaCount = method_exists($user, 'media') ? $user->media()->count() : 0;

        DB::transaction(function () use ($user, $postsCount, $mediaCount) {
            // 1) Insert audit log first (user still exists)
            AuditLog::create([
                'actor_id' => auth()->id(),
                'target_user_id' => $user->id,
                'action' => 'force_delete_user',
                'meta' => [
                    'posts_count' => $postsCount,
                    'media_count' => $mediaCount,
                    'ip' => request()->ip(),
                    'timestamp' => now()->toIso8601String(),
                    'warning' => 'Permanent deletion - user record will be removed from database',
                ],
            ]);

            // 2) Delete relationships/pivots (roles/permissions) if needed
            if (method_exists($user, 'roles')) {
                $user->roles()->detach();
            }
            if (method_exists($user, 'permissions')) {
                $user->permissions()->detach();
            }

            // 3) Finally permanently delete the user
            $user->forceDelete();
        });

        // Build message with newlines (JS will convert to <br>)
        if ($postsCount === 0) {
            $message = "تم حذف المستخدم نهائياً من قاعدة البيانات.";
        } else {
            $articleWord = $postsCount > 1 ? 'مقالات' : 'مقال';
            $message = "تم حذف المستخدم نهائياً من قاعدة البيانات.\nتم نقل {$postsCount} {$articleWord} إلى حساب المستخدم المحذوف.";
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', $message);
    }
}

