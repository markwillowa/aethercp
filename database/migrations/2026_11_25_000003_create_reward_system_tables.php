<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reward_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('category', 32)->index(); // level, job, pvp, mvp, guild, badge
            $table->string('trigger_type', 24)->default('badge'); // badge, tier
            $table->foreignId('badge_id')->nullable()->constrained('badge_definitions')->nullOnDelete();
            $table->foreignId('badge_tier_id')->nullable()->constrained('badge_tiers')->nullOnDelete();
            $table->string('reward_type', 24)->default('zeny'); // zeny, item, multi_item
            $table->unsignedBigInteger('zeny_amount')->default(0);
            $table->json('items_json')->nullable(); // [{"nameid": 501, "amount": 10}]
            $table->string('policy', 24)->default('one_time'); // one_time, per_season, per_period, repeatable
            $table->string('period_key', 32)->nullable();
            $table->string('message', 255)->nullable();
            $table->boolean('is_enabled')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('reward_issuances', function (Blueprint $table) {
            $table->id();
            $table->string('idempotency_key', 120)->unique();
            $table->string('transaction_ref', 32)->unique();
            $table->foreignId('reward_definition_id')->constrained('reward_definitions')->cascadeOnDelete();
            $table->string('target_type', 16); // character, guild
            $table->unsignedBigInteger('char_id')->nullable()->index();
            $table->unsignedBigInteger('account_id')->nullable()->index();
            $table->unsignedBigInteger('guild_id')->nullable()->index();
            $table->string('reward_type', 24);
            $table->unsignedBigInteger('zeny_amount')->default(0);
            $table->json('items_json')->nullable();
            $table->string('status', 24)->default('pending')->index(); // pending, processing, delivered, failed, retry_required
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('reward_delivery_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reward_issuance_id')->constrained('reward_issuances')->cascadeOnDelete();
            $table->unsignedTinyInteger('attempt_number');
            $table->string('status', 24); // delivered, failed
            $table->text('details')->nullable();
            $table->timestamp('attempted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_delivery_logs');
        Schema::dropIfExists('reward_issuances');
        Schema::dropIfExists('reward_definitions');
    }
};
