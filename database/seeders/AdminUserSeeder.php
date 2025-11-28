<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = env('ADMIN_NAME');
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (!$name || !$email || !$password) {
            $this->command->error('ADMIN_NAME, ADMIN_EMAIL and ADMIN_PASSWORD env vars must be set.');
            $this->command->error('Current values:');
            $this->command->error('  ADMIN_NAME: ' . ($name ?: 'NOT SET'));
            $this->command->error('  ADMIN_EMAIL: ' . ($email ?: 'NOT SET'));
            $this->command->error('  ADMIN_PASSWORD: ' . ($password ? 'SET (hidden)' : 'NOT SET'));
            return;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Also assign admin role using Spatie Permission
        if (!$user->hasRole('admin')) {
            $user->assignRole('admin');
        }

        $this->command->info("✓ Admin user created/updated: {$email}");
        $this->command->info("  Name: {$name}");
        $this->command->info("  Email: {$email}");
        $this->command->info("  Password: {$password}");
        $this->command->info("  Is Admin: " . ($user->is_admin ? 'Yes' : 'No'));
    }
}

