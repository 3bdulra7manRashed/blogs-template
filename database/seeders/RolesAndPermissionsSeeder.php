<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles - Only Super Admin (مدير النظام) and Moderator (مشرف)
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'web']
        );
        
        $moderatorRole = Role::firstOrCreate(
            ['name' => 'moderator'],
            ['guard_name' => 'web']
        );

        // Create Super Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'مدير النظام',
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
}

