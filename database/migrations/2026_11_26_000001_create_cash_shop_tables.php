<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Cash Shop Categories
        Schema::create('cash_shop_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('icon', 50)->default('tag');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        // 2. Cash Shop Products
        Schema::create('cash_shop_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('cash_shop_categories')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('item_id')->index();
            $table->unsignedInteger('item_quantity')->default(1);
            $table->unsignedInteger('cash_points_price')->default(0);
            $table->unsignedInteger('promotional_price')->nullable();
            $table->timestamp('promotion_start_at')->nullable();
            $table->timestamp('promotion_end_at')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->boolean('unlimited_stock')->default(true);
            $table->unsignedInteger('purchase_limit_per_account')->default(0);
            $table->unsignedInteger('purchase_limit_daily')->default(0);
            $table->json('item_metadata')->nullable();
            $table->string('image_url', 255)->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. Cash Shop Purchases (Audit Ledger & Delivery Link)
        Schema::create('cash_shop_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_code', 32)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('account_id')->index();
            $table->foreignId('product_id')->constrained('cash_shop_products')->cascadeOnDelete();
            $table->string('product_name_snapshot', 150);
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('item_quantity')->default(1);
            $table->unsignedInteger('cash_points_spent');
            $table->unsignedBigInteger('escrow_item_id')->nullable()->index();
            $table->enum('status', ['completed', 'failed', 'refunded'])->default('completed')->index();
            $table->string('ip_address', 45)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_shop_purchases');
        Schema::dropIfExists('cash_shop_products');
        Schema::dropIfExists('cash_shop_categories');
    }
};
