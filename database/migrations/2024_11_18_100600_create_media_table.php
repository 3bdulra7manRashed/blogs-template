<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('filename');
            $table->string('disk')->default('public')->index();
            $table->string('path');
            $table->unsignedBigInteger('size')->default(0);
            $table->string('mime_type', 191)->nullable();
            $table->string('caption')->nullable();
            $table->string('alt_text')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};

