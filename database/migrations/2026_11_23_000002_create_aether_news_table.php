<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('category', ['news', 'announcement', 'event', 'update', 'maintenance'])->default('news');
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->string('image')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->string('author_name')->default('Admin');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['category', 'is_published']);
            $table->index('is_pinned');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};

