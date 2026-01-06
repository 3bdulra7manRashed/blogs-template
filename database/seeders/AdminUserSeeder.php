<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Use env values with hardcoded defaults for the main admin
        $name = env('ADMIN_NAME', 'Saleh Alshehri');
        $email = env('ADMIN_EMAIL', 'admin@alshehri.com');
        $password = env('ADMIN_PASSWORD', 'password');

        // Ensure admin role exists
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // Check if user already exists to preserve profile_photo_path
        $existingUser = User::where('email', $email)->first();

        $userData = [
            'name' => $name,
            'password' => Hash::make($password),
            'is_admin' => true,
            'is_super_admin' => true,
            'role' => UserRole::ADMIN,
            'email_verified_at' => now(),
        ];

        // Only set profile_photo_path if user doesn't exist (preserve existing photo)
        if (!$existingUser) {
            $userData['profile_photo_path'] = null;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            $userData
        );

        // Assign Spatie admin role if not already assigned
        if (!$user->hasRole('admin')) {
            $user->assignRole($role);
        }

        $this->command->info("✓ Admin user '{$name}' (ID: {$user->id}) created/updated with Role: Admin, Super Admin: Yes");
    }
}