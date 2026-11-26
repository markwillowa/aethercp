<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->enum('identity_mode', ['username', 'character'])->default('username');
            $table->unsignedInteger('selected_char_id')->nullable()->index();
            $table->integer('reputation_points')->default(0)->index();
            $table->string('forum_title', 80)->nullable();
            $table->string('signature', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('reputation_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('slug', 80)->unique();
            $table->integer('min_points')->default(0)->index();
            $table->string('badge_color', 32)->default('#64748b');
            $table->string('badge_icon', 40)->default('award');
            $table->string('badge_style', 32)->default('solid');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('reputation_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipient_user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('source_user_id')->nullable()->index();
            $table->string('event_type', 64)->index();
            $table->integer('points');
            $table->unsignedBigInteger('topic_id')->nullable()->index();
            $table->unsignedBigInteger('post_id')->nullable()->index();
            $table->string('event_reference', 120)->unique();
            $table->string('description', 255);
            $table->boolean('is_reversal')->default(false);
            $table->unsignedBigInteger('reversed_transaction_id')->nullable();
            $table->timestamps();

            $table->index(['recipient_user_id', 'created_at'], 'rep_tx_recipient_created_idx');
            $table->index(['event_type', 'created_at'], 'rep_tx_event_created_idx');
        });

        Schema::create('reputation_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recipient_user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('points_delta');
            $table->integer('previous_total');
            $table->integer('new_total');
            $table->string('reason', 255);
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reputation_audit_logs');
        Schema::dropIfExists('reputation_transactions');
        Schema::dropIfExists('reputation_levels');
        Schema::dropIfExists('forum_user_profiles');
    }
};
