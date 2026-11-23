<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donation_packages', function (Blueprint $table) {
            $table->unsignedInteger('item_id')->nullable()->after('bonus_points');
            $table->unsignedInteger('item_amount')->default(0)->after('item_id');
            $table->string('item_name', 100)->nullable()->after('item_amount');
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->boolean('points_delivered')->default(false)->after('points_awarded');
            $table->boolean('items_delivered')->default(false)->after('points_delivered');
            $table->string('payer_email')->nullable()->after('transaction_reference');
            $table->string('payer_id')->nullable()->after('payer_email');
            $table->text('ipn_data')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['points_delivered', 'items_delivered', 'payer_email', 'payer_id', 'ipn_data']);
        });

        Schema::table('donation_packages', function (Blueprint $table) {
            $table->dropColumn(['item_id', 'item_amount', 'item_name']);
        });
    }
};

