<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Renames 'editor' role to 'moderator' and updates all user assignments.
     */
    public function up(): void
    {
        DB::transaction(function () {
            // Find the 'editor' role
            $editorRole = Role::where('name', 'editor')->where('guard_name', 'web')->first();
            
            if (!$editorRole) {
                echo "No 'editor' role found. Skipping migration.\n";
                return;
            }

            // Count users with editor role
            $affectedCount = DB::table('model_has_roles')
                ->where('role_id', $editorRole->id)
                ->count();

            echo "Found {$affectedCount} user(s) with 'editor' role.\n";

            // Simply rename the role
            $editorRole->name = 'moderator';
            $editorRole->save();

            echo "Renamed 'editor' role to 'moderator'. {$affectedCount} user(s) now have 'moderator' role.\n";
        });

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     * 
     * Renames 'moderator' back to 'editor'.
     */
    public function down(): void
    {
        DB::transaction(function () {
            $moderatorRole = Role::where('name', 'moderator')->where('guard_name', 'web')->first();
            
            if ($moderatorRole) {
                $moderatorRole->name = 'editor';
                $moderatorRole->save();
                echo "Renamed 'moderator' role back to 'editor'.\n";
            }
        });

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};

