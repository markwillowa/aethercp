<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('character_service_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('idempotency_key', 120)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('account_id')->index();
            $table->unsignedBigInteger('char_id')->index();
            $table->string('char_name', 64)->nullable();
            $table->string('service_type', 40)->index(); // rename, appearance, stat_reset, skill_reset, gender_change, deletion, recovery
            $table->unsignedInteger('cash_points_cost')->default(0);
            $table->string('status', 24)->default('pending')->index(); // pending, completed, failed, refunded
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('failure_reason')->nullable();
            $table->unsignedBigInteger('refunded_by')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['char_id', 'service_type', 'created_at'], 'char_svc_cooldown_idx');
        });

        Schema::create('character_service_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('actor_user_id')->nullable()->index();
            $table->string('actor_type', 20)->default('player'); // player, admin, system
            $table->unsignedBigInteger('account_id')->nullable()->index();
            $table->unsignedBigInteger('char_id')->index();
            $table->string('action_type', 64)->index();
            $table->json('previous_values')->nullable();
            $table->json('updated_values')->nullable();
            $table->unsignedInteger('service_cost')->default(0);
            $table->string('transaction_reference', 120)->nullable();
            $table->string('result', 24)->default('success'); // success, failed
            $table->text('failure_reason')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('character_locks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('char_id')->index();
            $table->unsignedBigInteger('account_id')->index();
            $table->boolean('is_locked')->default(true)->index();
            $table->string('lock_scope', 24)->default('services'); // services, full
            $table->string('reason', 255);
            $table->unsignedBigInteger('locked_by')->nullable();
            $table->timestamp('locked_at');
            $table->unsignedBigInteger('unlocked_by')->nullable();
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('character_deletion_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('account_id')->index();
            $table->unsignedBigInteger('char_id')->index();
            $table->string('char_name', 64);
            $table->string('status', 24)->default('pending')->index(); // pending, cancelled, executed, recovered
            $table->timestamp('requested_at');
            $table->timestamp('scheduled_delete_at')->index();
            $table->timestamp('executed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('recovery_status', 24)->nullable(); // pending_approval, approved, rejected
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('character_admin_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('char_id')->index();
            $table->foreignId('admin_user_id')->constrained('users')->cascadeOnDelete();
            $table->text('note');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_admin_notes');
        Schema::dropIfExists('character_deletion_requests');
        Schema::dropIfExists('character_locks');
        Schema::dropIfExists('character_service_logs');
        Schema::dropIfExists('character_service_transactions');
    }
};
