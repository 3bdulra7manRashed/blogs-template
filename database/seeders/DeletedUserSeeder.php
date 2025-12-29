<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DeletedUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('DELETED_USER_EMAIL', 'deleted-user@local');

        // Check if placeholder exists (including soft-deleted)
        $user = User::withTrashed()->firstWhere('email', $email);

        if (!$user) {
            User::create([
                'name' => 'Deleted User',
                'email' => $email,
                'password' => Hash::make(Str::random(40)),
                'is_admin' => false,
                'is_super_admin' => false,
                'email_verified_at' => now(),
            ]);
            $this->command->info("✅ Deleted User placeholder created: {$email}");
        } else {
            // Ensure it's not soft-deleted
            if ($user->trashed()) {
                $user->restore();
                $this->command->info("✅ Restored existing Deleted User placeholder: {$email}");
            } else {
                $this->command->info("ℹ️  Deleted User placeholder already exists: {$email}");
            }
        }
    }
}

