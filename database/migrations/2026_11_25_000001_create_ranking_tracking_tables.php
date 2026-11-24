<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ranking_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_uuid', 64)->unique();
            $table->string('event_type', 40)->index(); // max_base_level, max_job_level, pvp_kill, mvp_kill, guild_level, guild_castle, guild_member
            $table->unsignedBigInteger('char_id')->nullable()->index();
            $table->unsignedBigInteger('account_id')->nullable()->index();
            $table->unsignedBigInteger('target_char_id')->nullable()->index();
            $table->unsignedBigInteger('guild_id')->nullable()->index();
            $table->unsignedBigInteger('mob_id')->nullable();
            $table->bigInteger('value')->default(1);
            $table->json('payload')->nullable();
            $table->string('status', 24)->default('pending')->index(); // processed, rejected, failed, pending
            $table->string('rejection_reason')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ranking_character_stats', function (Blueprint $table) {
            $table->unsignedBigInteger('char_id')->primary();
            $table->unsignedBigInteger('account_id')->index();
            $table->unsignedInteger('pvp_kills')->default(0)->index();
            $table->unsignedInteger('pvp_deaths')->default(0);
            $table->unsignedInteger('mvp_kills')->default(0)->index();
            $table->timestamp('max_base_reached_at')->nullable()->index();
            $table->unsignedInteger('max_base_level_achieved')->nullable();
            $table->timestamp('max_job_reached_at')->nullable()->index();
            $table->unsignedInteger('max_job_level_achieved')->nullable();
            $table->timestamp('last_event_at')->nullable();
            $table->timestamps();
        });

        Schema::create('emulator_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('sync_type', 40)->index(); // event_ingest, guild_sync, reconciliation
            $table->string('status', 24)->index(); // success, failed, partial
            $table->unsignedInteger('events_processed')->default(0);
            $table->unsignedInteger('events_rejected')->default(0);
            $table->text('error_message')->nullable();
            $table->unsignedInteger('duration_ms')->default(0);
            $table->timestamp('synced_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emulator_sync_logs');
        Schema::dropIfExists('ranking_character_stats');
        Schema::dropIfExists('ranking_events');
    }
};
