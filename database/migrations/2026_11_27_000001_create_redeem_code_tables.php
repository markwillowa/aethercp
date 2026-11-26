<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redeem_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 170)->unique();
            $table->text('description')->nullable();
            $table->string('banner_url', 500)->nullable();
            $table->string('campaign_type', 40)->default('general_promotion')->index();
            $table->text('instructions')->nullable();
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('ends_at')->nullable()->index();
            $table->string('status', 24)->default('draft')->index(); // draft, scheduled, active, ended, disabled
            $table->boolean('is_public')->default(false)->index();

            // Restrictions
            $table->unsignedInteger('max_total_redemptions')->nullable();
            $table->unsignedInteger('max_per_account')->default(1);
            $table->boolean('one_per_account')->default(true);
            $table->boolean('one_per_character')->default(false);
            $table->json('allowed_account_ids')->nullable();
            $table->json('allowed_character_ids')->nullable();
            $table->string('account_age_rule', 24)->default('any'); // any, new_only, existing_only
            $table->timestamp('account_created_after')->nullable();
            $table->timestamp('account_created_before')->nullable();
            $table->unsignedInteger('min_account_age_days')->nullable();
            $table->unsignedInteger('min_base_level')->nullable();
            $table->unsignedInteger('max_base_level')->nullable();
            $table->json('allowed_classes')->nullable();
            $table->json('excluded_classes')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('redeem_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('redeem_campaigns')->cascadeOnDelete();
            $table->char('code_hash', 64)->unique();
            $table->string('code_prefix', 32)->nullable()->index();
            $table->string('code_hint', 40);
            $table->string('code_type', 20)->default('single')->index(); // single, bulk, shared
            $table->unsignedInteger('max_redemptions')->default(1);
            $table->unsignedInteger('redemptions_count')->default(0);
            $table->unsignedBigInteger('bound_account_id')->nullable()->index();
            $table->timestamp('expires_at')->nullable()->index();
            $table->string('status', 24)->default('active')->index(); // active, disabled, expired, exhausted
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'status']);
        });

        Schema::create('redeem_code_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('redeem_campaigns')->cascadeOnDelete();
            $table->foreignId('redeem_code_id')->nullable()->constrained('redeem_codes')->cascadeOnDelete();
            $table->string('reward_type', 24)->index(); // cash_points, zeny, item, custom
            $table->unsignedBigInteger('amount')->default(0);
            $table->string('delivery_scope', 20)->default('account'); // account, character
            $table->unsignedInteger('item_id')->nullable()->index();
            $table->string('item_name', 120)->nullable();
            $table->unsignedTinyInteger('refine')->default(0);
            $table->unsignedInteger('card0')->default(0);
            $table->unsignedInteger('card1')->default(0);
            $table->unsignedInteger('card2')->default(0);
            $table->unsignedInteger('card3')->default(0);
            $table->boolean('is_bound')->default(false);
            $table->json('custom_payload')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('redeem_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('redeem_campaigns')->cascadeOnDelete();
            $table->foreignId('redeem_code_id')->constrained('redeem_codes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('account_id')->index();
            $table->string('username_snapshot', 64)->nullable();
            $table->unsignedBigInteger('character_id')->nullable()->index();
            $table->string('character_name_snapshot', 64)->nullable();
            $table->string('code_hint', 40);
            $table->string('status', 24)->default('pending')->index(); // pending, processing, completed, partially_failed, failed, cancelled
            $table->string('delivery_status', 24)->default('pending')->index(); // pending, processing, delivered, failed, retrying
            $table->string('idempotency_key', 120)->unique();
            $table->string('ip_address', 45)->nullable();
            $table->json('reward_summary')->nullable();
            $table->timestamp('redeemed_at')->nullable()->index();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['redeem_code_id', 'account_id']);
            $table->index(['campaign_id', 'account_id']);
        });

        Schema::create('redeem_reward_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('redemption_id')->constrained('redeem_redemptions')->cascadeOnDelete();
            $table->foreignId('reward_id')->nullable()->constrained('redeem_code_rewards')->nullOnDelete();
            $table->string('reward_type', 24)->index();
            $table->string('idempotency_key', 120)->unique();
            $table->string('status', 24)->default('pending')->index(); // pending, processing, delivered, failed, retrying
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedBigInteger('account_id')->index();
            $table->unsignedBigInteger('character_id')->nullable()->index();
            $table->unsignedBigInteger('amount')->default(0);
            $table->unsignedInteger('item_id')->nullable();
            $table->string('item_name', 120)->nullable();
            $table->json('payload')->nullable();
            $table->string('destination_reference', 255)->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('redeem_campaign_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained('redeem_campaigns')->nullOnDelete();
            $table->foreignId('redeem_code_id')->nullable()->constrained('redeem_codes')->nullOnDelete();
            $table->foreignId('redemption_id')->nullable()->constrained('redeem_redemptions')->nullOnDelete();
            $table->unsignedBigInteger('actor_user_id')->nullable()->index();
            $table->string('action', 64)->index();
            $table->text('details')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redeem_campaign_logs');
        Schema::dropIfExists('redeem_reward_deliveries');
        Schema::dropIfExists('redeem_redemptions');
        Schema::dropIfExists('redeem_code_rewards');
        Schema::dropIfExists('redeem_codes');
        Schema::dropIfExists('redeem_campaigns');
    }
};
