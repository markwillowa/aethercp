<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Attendance Campaigns
        Schema::create('attendance_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('duration_days')->default(7);
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->string('timezone', 64)->default('UTC');
            $table->string('status', 24)->default('draft')->index(); // draft, scheduled, active, paused, completed
            $table->string('progression_mode', 24)->default('cumulative')->index(); // consecutive, cumulative, scheduled
            $table->string('reset_rule', 24)->default('none'); // none, reset_to_start, freeze_streak
            $table->string('participation_scope', 24)->default('account')->index(); // account, character
            $table->string('delivery_destination', 32)->default('inbox'); // inbox, storage, character
            $table->boolean('allow_character_choice')->default(true);
            $table->boolean('require_in_game_login')->default(false);
            $table->boolean('repeatable')->default(false);
            $table->integer('display_order')->default(0);
            $table->unsignedInteger('min_base_level')->default(1);
            $table->unsignedInteger('min_account_age_days')->default(0);
            $table->unsignedSmallInteger('daily_reset_hour')->default(0); // 0-23
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // 2. Campaign Days
        Schema::create('attendance_campaign_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('attendance_campaigns')->onDelete('cascade');
            $table->unsignedInteger('day_number')->index();
            $table->string('title', 120)->nullable();
            $table->string('description', 255)->nullable();
            $table->boolean('is_milestone')->default(false);
            $table->timestamps();

            $table->unique(['campaign_id', 'day_number']);
        });

        // 3. Campaign Items / Rewards (Multiple per day)
        Schema::create('attendance_campaign_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_day_id')->constrained('attendance_campaign_days')->onDelete('cascade');
            $table->string('reward_type', 24)->default('item'); // zeny, item, cashpoints
            $table->unsignedInteger('item_id')->nullable();
            $table->string('item_name', 120)->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('zeny_amount')->default(0);
            $table->unsignedInteger('cashpoints_amount')->default(0);
            $table->unsignedTinyInteger('refine')->default(0);
            $table->unsignedSmallInteger('card0')->default(0);
            $table->unsignedSmallInteger('card1')->default(0);
            $table->unsignedSmallInteger('card2')->default(0);
            $table->unsignedSmallInteger('card3')->default(0);
            $table->boolean('identified')->default(true);
            $table->unsignedTinyInteger('bound')->default(0);
            $table->string('delivery_method', 32)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // 4. Player Participations
        Schema::create('attendance_participations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('attendance_campaigns')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('account_id')->index();
            $table->unsignedBigInteger('character_id')->default(0)->index();
            $table->unsignedInteger('current_progress')->default(0);
            $table->unsignedInteger('current_streak')->default(0);
            $table->unsignedInteger('highest_streak')->default(0);
            $table->unsignedInteger('total_claimed_days')->default(0);
            $table->date('last_attendance_date')->nullable()->index();
            $table->dateTime('last_claimed_at')->nullable();
            $table->dateTime('started_at');
            $table->dateTime('completed_at')->nullable();
            $table->string('status', 24)->default('active')->index(); // active, completed, abandoned
            $table->timestamps();

            $table->unique(['campaign_id', 'account_id', 'character_id'], 'att_part_unique');
        });

        // 5. Attendance Claims
        Schema::create('attendance_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participation_id')->constrained('attendance_participations')->onDelete('cascade');
            $table->unsignedBigInteger('campaign_id')->index();
            $table->unsignedBigInteger('campaign_day_id')->index();
            $table->unsignedInteger('day_number');
            $table->unsignedBigInteger('account_id')->index();
            $table->unsignedBigInteger('character_id')->nullable()->index();
            $table->dateTime('claimed_at')->index();
            $table->string('status', 24)->default('delivered')->index(); // delivered, partial, failed, pending_inbox
            $table->string('idempotency_key', 120)->unique();
            $table->timestamps();

            $table->unique(['participation_id', 'day_number'], 'att_claim_part_day_unique');
        });

        // 6. Deliveries (Tracks every single reward item in a day's claim individually)
        Schema::create('attendance_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained('attendance_claims')->onDelete('cascade');
            $table->unsignedBigInteger('campaign_item_id')->nullable()->index();
            $table->string('reward_type', 24);
            $table->string('delivery_type', 32); // inbox, storage, character_inventory, zeny, cashpoints
            $table->string('destination_target', 190)->nullable();
            $table->string('status', 24)->default('delivered')->index(); // delivered, pending, failed, retrying
            $table->unsignedSmallInteger('attempts')->default(1);
            $table->dateTime('delivered_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->string('external_reference', 120)->nullable();
            $table->timestamps();
        });

        // 7. Player Inbox Items (Safest default for web claiming into in-game Storage)
        Schema::create('player_inbox_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('account_id')->index();
            $table->unsignedBigInteger('character_id')->nullable()->index();
            $table->string('title', 120);
            $table->text('message')->nullable();
            $table->string('source_type', 32)->default('attendance')->index(); // attendance, admin_gift, system
            $table->unsignedBigInteger('source_id')->nullable()->index();
            $table->string('reward_type', 24)->default('item'); // item, zeny, cashpoints
            $table->unsignedInteger('item_id')->nullable();
            $table->string('item_name', 120)->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('zeny_amount')->default(0);
            $table->unsignedInteger('cashpoints_amount')->default(0);
            $table->unsignedTinyInteger('refine')->default(0);
            $table->unsignedSmallInteger('card0')->default(0);
            $table->unsignedSmallInteger('card1')->default(0);
            $table->unsignedSmallInteger('card2')->default(0);
            $table->unsignedSmallInteger('card3')->default(0);
            $table->boolean('identified')->default(true);
            $table->unsignedTinyInteger('bound')->default(0);
            $table->string('status', 24)->default('unclaimed')->index(); // unclaimed, claimed, expired
            $table->string('claimed_to', 32)->nullable(); // storage, character
            $table->unsignedBigInteger('claimed_character_id')->nullable();
            $table->dateTime('claimed_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_inbox_items');
        Schema::dropIfExists('attendance_deliveries');
        Schema::dropIfExists('attendance_claims');
        Schema::dropIfExists('attendance_participations');
        Schema::dropIfExists('attendance_campaign_items');
        Schema::dropIfExists('attendance_campaign_days');
        Schema::dropIfExists('attendance_campaigns');
    }
};
