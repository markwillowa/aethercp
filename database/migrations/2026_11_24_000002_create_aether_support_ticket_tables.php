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
        // 1. Support Tickets
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_reference', 32)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('account_id')->index();
            $table->unsignedInteger('char_id')->nullable()->index();
            $table->string('char_name', 50)->nullable();
            $table->string('subject', 200);
            $table->string('category', 50)->default('other')->index();
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->index();
            $table->enum('status', ['open', 'in_progress', 'awaiting_player', 'awaiting_staff', 'resolved', 'closed'])->default('open')->index();
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('related_reference', 100)->nullable();
            $table->string('map_name', 50)->nullable();
            $table->string('incident_time', 50)->nullable();
            $table->timestamp('last_message_at')->useCurrent()->index();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // 2. Support Ticket Messages
        Schema::create('support_ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('sender_type', ['player', 'staff', 'system'])->default('player');
            $table->string('sender_name', 100);
            $table->text('message');
            $table->boolean('is_internal')->default(false)->index();
            $table->timestamps();
        });

        // 3. Support Ticket Attachments
        Schema::create('support_ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets')->cascadeOnDelete();
            $table->foreignId('message_id')->constrained('support_ticket_messages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('original_filename', 255);
            $table->string('stored_filename', 255);
            $table->string('storage_path', 255);
            $table->string('mime_type', 100);
            $table->unsignedInteger('file_size');
            $table->timestamps();
        });

        // 4. Support Ticket Activity Audit Trail
        Schema::create('support_ticket_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('activity_type', 50);
            $table->string('old_value', 100)->nullable();
            $table->string('new_value', 100)->nullable();
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_ticket_activities');
        Schema::dropIfExists('support_ticket_attachments');
        Schema::dropIfExists('support_ticket_messages');
        Schema::dropIfExists('support_tickets');
    }
};
