<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop old fields if they exist
            $table->dropColumn(['bio', 'bio_html']);
            if (Schema::hasColumn('users', 'about')) {
                $table->dropColumn('about');
            }

            // Add new fields
            $table->text('short_bio')->nullable()->after('email');
            $table->longText('biography')->nullable()->after('short_bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['short_bio', 'biography']);
            $table->text('bio')->nullable();
            $table->text('bio_html')->nullable();
        });
    }
};
