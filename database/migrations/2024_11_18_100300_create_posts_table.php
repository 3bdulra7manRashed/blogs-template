<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('title', 180);
            $table->string('slug', 200)->unique();
            $table->string('excerpt', 400)->nullable();
            $table->longText('content');
            $table->string('featured_image_path')->nullable();
            $table->string('featured_image_alt')->nullable();
            $table->boolean('is_draft')->default(true)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->unsignedInteger('read_time')->default(1);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

