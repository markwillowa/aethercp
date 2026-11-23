<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Marketplace Items (Escrow & Custody)
        Schema::create('marketplace_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('account_id')->index();
            $table->unsignedInteger('char_id')->index();
            $table->unsignedInteger('nameid')->index();
            $table->string('item_name', 150)->default('');
            $table->unsignedInteger('amount')->default(1);
            $table->unsignedInteger('equip')->default(0);
            $table->unsignedTinyInteger('identify')->default(1);
            $table->unsignedTinyInteger('refine')->default(0);
            $table->unsignedTinyInteger('attribute')->default(0);
            $table->unsignedInteger('card0')->default(0);
            $table->unsignedInteger('card1')->default(0);
            $table->unsignedInteger('card2')->default(0);
            $table->unsignedInteger('card3')->default(0);
            $table->unsignedBigInteger('expire_time')->default(0);
            $table->unsignedTinyInteger('bound')->default(0);
            $table->unsignedBigInteger('unique_id')->default(0);
            $table->string('item_type', 50)->default('Etc');
            $table->enum('status', ['in_custody', 'listed', 'sold', 'withdrawn', 'cancelled'])->default('in_custody')->index();
            $table->timestamp('deposited_at')->useCurrent();
            $table->timestamp('withdrawn_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // 2. Marketplace Listings (Active & Concluded Listings)
        Schema::create('marketplace_listings', function (Blueprint $table) {
            $table->id();
            $table->string('listing_code', 32)->unique();
            $table->foreignId('item_id')->constrained('marketplace_items')->cascadeOnDelete();
            $table->foreignId('seller_user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('seller_account_id')->index();
            $table->unsignedInteger('seller_char_id')->index();
            $table->string('seller_char_name', 50);
            $table->enum('currency_type', ['zeny', 'cashpoints'])->default('zeny')->index();
            $table->unsignedBigInteger('unit_price')->default(0);
            $table->unsignedBigInteger('total_price')->default(0);
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('remaining_quantity')->default(1);
            $table->unsignedInteger('duration_hours')->default(72);
            $table->enum('status', ['active', 'sold', 'cancelled', 'expired'])->default('active')->index();
            $table->timestamp('expires_at')->index();
            $table->timestamp('sold_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });

        // 3. Marketplace Transactions (Ledger & Audit Trail)
        Schema::create('marketplace_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 32)->unique();
            $table->foreignId('listing_id')->constrained('marketplace_listings')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('marketplace_items')->cascadeOnDelete();
            $table->foreignId('seller_user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('seller_account_id')->index();
            $table->unsignedInteger('seller_char_id')->index();
            $table->string('seller_char_name', 50);
            $table->foreignId('buyer_user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('buyer_account_id')->index();
            $table->unsignedInteger('buyer_char_id')->index();
            $table->string('buyer_char_name', 50);
            $table->enum('currency_type', ['zeny', 'cashpoints'])->default('zeny');
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('unit_price')->default(0);
            $table->unsignedBigInteger('gross_amount')->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0.00);
            $table->unsignedBigInteger('tax_amount')->default(0);
            $table->unsignedBigInteger('net_seller_proceeds')->default(0);
            $table->enum('status', ['completed', 'failed', 'refunded'])->default('completed');
            $table->string('delivery_status', 50)->default('delivered_to_storage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_transactions');
        Schema::dropIfExists('marketplace_listings');
        Schema::dropIfExists('marketplace_items');
    }
};
