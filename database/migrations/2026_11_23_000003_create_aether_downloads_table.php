<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('category', ['Full Client', 'Lite Client', 'Manual Patch', 'Tools & Addons'])->default('Full Client');
            $table->string('version')->nullable();
            $table->string('file_size')->nullable();
            $table->string('url_mirror1');
            $table->string('url_mirror2')->nullable();
            $table->string('url_mirror3')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category', 'is_active']);
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};

