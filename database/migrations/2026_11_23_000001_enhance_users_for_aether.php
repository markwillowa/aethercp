<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('account_id')->nullable()->unique()->after('id');
            $table->string('username', 23)->unique()->after('account_id');
            $table->enum('role', ['admin', 'staff', 'user'])->default('user')->after('password');
            $table->string('avatar')->nullable()->after('role');
            $table->string('last_login_ip', 45)->nullable()->after('avatar');
            $table->timestamp('last_login_at')->nullable()->after('last_login_ip');
            $table->boolean('is_active')->default(true)->after('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['account_id', 'username', 'role', 'avatar', 'last_login_ip', 'last_login_at', 'is_active']);
        });
    }
};

