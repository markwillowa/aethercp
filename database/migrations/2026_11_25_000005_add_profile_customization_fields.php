<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('player_profile_settings', function (Blueprint $table) {
            $table->boolean('show_equipments')->default(true)->after('show_char_info');
            $table->boolean('show_zeny')->default(true)->after('show_equipments');
            $table->string('avatar_path', 255)->nullable()->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('player_profile_settings', function (Blueprint $table) {
            $table->dropColumn(['show_equipments', 'show_zeny', 'avatar_path']);
        });
    }
};
