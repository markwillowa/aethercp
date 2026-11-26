<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->default('message-square');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('forum_boards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('forum_categories')->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('slug', 140)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->default('hash');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_locked')->default(false);
            $table->json('allow_topics_roles')->nullable();
            $table->unsignedInteger('topics_count')->default(0);
            $table->unsignedInteger('posts_count')->default(0);
            $table->unsignedBigInteger('last_topic_id')->nullable()->index();
            $table->timestamp('last_post_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained('forum_boards')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->boolean('is_pinned')->default(false)->index();
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_solved')->default(false)->index();
            $table->boolean('is_announcement')->default(false)->index();
            $table->boolean('is_approved_guide')->default(false);
            $table->unsignedBigInteger('accepted_post_id')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('replies_count')->default(0);
            $table->unsignedBigInteger('last_post_id')->nullable();
            $table->unsignedBigInteger('last_post_user_id')->nullable();
            $table->timestamp('last_activity_at')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['board_id', 'is_pinned', 'last_activity_at'], 'forum_topics_board_listing_idx');
            $table->index(['user_id', 'created_at'], 'forum_topics_user_created_idx');
        });

        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('forum_topics')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->longText('content');
            $table->boolean('is_first_post')->default(false)->index();
            $table->boolean('is_accepted_answer')->default(false);
            $table->unsignedInteger('likes_count')->default(0);
            $table->timestamp('edited_at')->nullable();
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['topic_id', 'created_at'], 'forum_posts_topic_created_idx');
            $table->index(['user_id', 'created_at'], 'forum_posts_user_created_idx');
        });

        Schema::create('forum_post_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('forum_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 32)->default('like');
            $table->timestamps();

            $table->unique(['post_id', 'user_id', 'type'], 'forum_post_reactions_unique');
        });

        Schema::create('forum_post_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('forum_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('original_name', 255);
            $table->string('file_path', 500);
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->timestamps();
        });

        Schema::create('forum_topic_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('forum_topics')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['topic_id', 'user_id'], 'forum_topic_subscriptions_unique');
        });

        Schema::create('forum_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('topic_id')->nullable()->index();
            $table->unsignedBigInteger('post_id')->nullable()->index();
            $table->string('reason', 100);
            $table->text('details')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'dismissed'])->default('pending')->index();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_reports');
        Schema::dropIfExists('forum_topic_subscriptions');
        Schema::dropIfExists('forum_post_attachments');
        Schema::dropIfExists('forum_post_reactions');
        Schema::dropIfExists('forum_posts');
        Schema::dropIfExists('forum_topics');
        Schema::dropIfExists('forum_boards');
        Schema::dropIfExists('forum_categories');
    }
};
