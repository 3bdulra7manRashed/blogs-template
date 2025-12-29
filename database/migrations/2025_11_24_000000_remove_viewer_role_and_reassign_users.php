<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration safely removes the 'user' role (viewer) and reassigns
     * all users with that role to the 'editor' role.
     */
    public function up(): void
    {
        DB::transaction(function () {
            // Find the 'user' role (equivalent to viewer)
            $userRole = Role::where('name', 'user')->first();
            
            if (!$userRole) {
                echo "No 'user' role found. Skipping migration.\n";
                return;
            }

            // Ensure 'editor' role exists
            $editorRole = Role::firstOrCreate(
                ['name' => 'editor'],
                ['guard_name' => 'web']
            );

            // Get count of users with 'user' role before reassignment
            $affectedCount = DB::table('model_has_roles')
                ->where('role_id', $userRole->id)
                ->count();

            echo "Found {$affectedCount} user(s) with 'user' role.\n";

            if ($affectedCount > 0) {
                // Reassign all users from 'user' role to 'editor' role
                DB::table('model_has_roles')
                    ->where('role_id', $userRole->id)
                    ->update(['role_id' => $editorRole->id]);

                echo "Reassigned {$affectedCount} user(s) to 'editor' role.\n";
            }

            // Delete the 'user' role
            $userRole->delete();
            echo "Deleted 'user' role successfully.\n";
        });
    }

    /**
     * Reverse the migrations.
     * 
     * This will recreate the 'user' role but will NOT reassign users back
     * (as we don't track which users were originally 'user' vs 'editor').
     */
    public function down(): void
    {
        // Recreate the 'user' role
        Role::firstOrCreate(
            ['name' => 'user'],
            ['guard_name' => 'web']
        );

        echo "Recreated 'user' role. Note: Users were not reassigned back.\n";
    }
};

