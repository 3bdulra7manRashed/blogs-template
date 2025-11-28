<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        // Make the first user a super admin
        $user = User::find(1);
        if ($user) {
            $user->is_super_admin = true;
            $user->save();
        }
    }

    public function down(): void
    {
        $user = User::find(1);
        if ($user) {
            $user->is_super_admin = false;
            $user->save();
        }
    }
};

