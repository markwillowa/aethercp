<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badge_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 64)->unique();
            $table->string('name', 80);
            $table->string('description', 255)->nullable();
            $table->string('category', 32)->index(); // level, job, pvp, mvp, guild, special, event, achievement
            $table->string('icon_type', 16)->default('lucide'); // lucide, image
            $table->string('icon', 255)->default('award');
            $table->string('color', 32)->default('#06b6d4');
            $table->string('visual_style', 24)->default('solid'); // solid, outline, gradient, glow
            $table->integer('priority')->default(10)->index();
            $table->boolean('is_enabled')->default(true)->index();
            $table->boolean('is_permanent')->default(true);
            $table->string('auto_condition_type', 40)->nullable()->index(); // first_max_base, max_base, first_max_job, max_job, guild_rank_1, guild_rank_2, guild_rank_3, guild_rank_top10, manual
            $table->integer('auto_condition_value')->nullable();
            $table->timestamps();
        });

        Schema::create('badge_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('category', 16)->index(); // pvp, mvp
            $table->string('tier_name', 32); // Bronze, Silver, Gold, Platinum, Diamond
            $table->unsignedTinyInteger('tier_order'); // 1..5
            $table->unsignedTinyInteger('stars'); // 1..5
            $table->unsignedInteger('min_kills');
            $table->string('color', 32)->default('#cd7f32');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->unique(['category', 'tier_name', 'stars'], 'badge_tiers_cat_name_stars_unique');
        });

        Schema::create('badge_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('badge_id')->nullable()->constrained('badge_definitions')->nullOnDelete();
            $table->foreignId('badge_tier_id')->nullable()->constrained('badge_tiers')->nullOnDelete();
            $table->string('target_type', 16); // character, guild
            $table->unsignedBigInteger('target_id');
            $table->string('category', 32)->index();
            $table->string('label', 100);
            $table->unsignedTinyInteger('stars')->nullable();
            $table->string('color', 32)->default('#06b6d4');
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_permanent')->default(true);
            $table->string('assigned_by', 16)->default('system'); // system, admin
            $table->unsignedBigInteger('assigned_by_user_id')->nullable();
            $table->string('notes', 255)->nullable();
            $table->timestamp('awarded_at')->index();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['target_type', 'target_id', 'is_active'], 'badge_assignments_target_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badge_assignments');
        Schema::dropIfExists('badge_tiers');
        Schema::dropIfExists('badge_definitions');
    }
};
