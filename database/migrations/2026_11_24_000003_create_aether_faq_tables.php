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
        // 1. FAQ Categories
        Schema::create('faq_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->default('help-circle');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        // 2. FAQs
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('faq_categories')->nullOnDelete();
            $table->string('question');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('answer');
            $table->string('keywords')->nullable();
            $table->string('status')->default('published'); // draft, published, archived
            $table->boolean('is_featured')->default(false);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'is_featured']);
            $table->index(['category_id', 'status']);
            $table->index('views_count');
        });

        // 3. FAQ Feedback (Helpful / Not Helpful)
        Schema::create('faq_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faq_id')->constrained('faqs')->cascadeOnDelete();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('feedback_type', 20); // helpful, not_helpful
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['faq_id', 'account_id']);
            $table->index(['faq_id', 'ip_address']);
        });

        // 4. FAQ Related Questions (Manual relationships)
        Schema::create('faq_related_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faq_id')->constrained('faqs')->cascadeOnDelete();
            $table->foreignId('related_faq_id')->constrained('faqs')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);

            $table->unique(['faq_id', 'related_faq_id']);
        });

        // 5. FAQ Search Logs
        Schema::create('faq_search_logs', function (Blueprint $table) {
            $table->id();
            $table->string('search_term');
            $table->integer('results_count')->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('search_term');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faq_search_logs');
        Schema::dropIfExists('faq_related_questions');
        Schema::dropIfExists('faq_feedback');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('faq_categories');
    }
};
