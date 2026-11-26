<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_profile_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('char_id')->primary();
            $table->unsignedBigInteger('account_id')->index();
            $table->boolean('is_public')->default(true)->index();
            $table->boolean('show_char_info')->default(true);
            $table->boolean('show_equipments')->default(true);
            $table->boolean('show_zeny')->default(true);
            $table->boolean('show_rankings')->default(true);
            $table->boolean('show_badges')->default(true);
            $table->boolean('show_achievements')->default(true);
            $table->boolean('show_stats')->default(true);
            $table->boolean('show_guild')->default(true);
            $table->boolean('show_last_login')->default(false);
            $table->string('bio', 255)->nullable();
            $table->string('avatar_path', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('player_achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('char_id')->index();
            $table->string('achievement_key', 64)->index();
            $table->string('title', 100);
            $table->string('description', 255)->nullable();
            $table->string('category', 32)->default('milestone'); // level, job, pvp, mvp, guild, event, milestone
            $table->string('icon', 64)->default('trophy');
            $table->string('color', 32)->default('#f59e0b');
            $table->timestamp('earned_at')->index();
            $table->timestamps();

            $table->unique(['char_id', 'achievement_key'], 'player_achievements_char_key_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_achievements');
        Schema::dropIfExists('player_profile_settings');
    }
};
