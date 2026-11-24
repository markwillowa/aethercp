<?php

namespace App\Services\Support;

use App\Contracts\Repositories\CharacterRepositoryInterface;
use App\Models\SiteSetting;
use App\Models\SupportTicket;
use App\Models\SupportTicketActivity;
use App\Models\SupportTicketAttachment;
use App\Models\SupportTicketMessage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupportTicketService
{
    public function __construct(
        protected CharacterRepositoryInterface $characterRepo
    ) {}

    /**
     * Get all Support Center settings with defaults
     */
    public function getSettings(): array
    {
        return [
            'enabled' => (bool) SiteSetting::get('support_enabled', 1),
            'allow_player_replies' => (bool) SiteSetting::get('support_allow_player_replies', 1),
            'allow_reopen' => (bool) SiteSetting::get('support_allow_reopen', 1),
            'allow_close' => (bool) SiteSetting::get('support_allow_close', 1),
            'allow_attachments' => (bool) SiteSetting::get('support_allow_attachments', 1),
            'max_attachment_size_kb' => (int) SiteSetting::get('support_max_attachment_size_kb', 5120),
            'max_attachments_per_msg' => (int) SiteSetting::get('support_max_attachments_per_msg', 3),
            'allowed_extensions' => (string) SiteSetting::get('support_allowed_extensions', 'jpg,jpeg,png,webp,pdf,txt'),
            'max_open_per_account' => (int) SiteSetting::get('support_max_open_per_account', 5),
            'default_priority' => (string) SiteSetting::get('support_default_priority', 'normal'),
            'announcement' => (string) SiteSetting::get('support_announcement', 'Welcome to the AetherCP Player Support Center. Submit tickets, track investigations, and receive direct assistance from game masters and administrators.'),
            'support_email_notify' => (bool) SiteSetting::get('support_email_notify', 0),
        ];
    }

    /**
     * Update Support Center settings
     */
    public function updateSettings(array $data): void
    {
        $keys = [
            'support_enabled',
            'support_allow_player_replies',
            'support_allow_reopen',
            'support_allow_close',
            'support_allow_attachments',
            'support_max_attachment_size_kb',
            'support_max_attachments_per_msg',
            'support_allowed_extensions',
            'support_max_open_per_account',
            'support_default_priority',
            'support_announcement',
            'support_email_notify',
        ];

        foreach ($keys as $k) {
            $short = str_replace('support_', '', $k);
            if (isset($data[$short])) {
                SiteSetting::set($k, (string) $data[$short]);
            }
        }
    }

    /**
     * Get characters owned by the authenticated account
     */
    public function getAccountCharacters(int $accountId): array
    {
        try {
            return $this->characterRepo->getByAccountId($accountId);
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Get ticket statistics
     */
    public function getStatistics(?int $userId = null): array
    {
        $query = SupportTicket::query();
        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        $all = (clone $query)->count();
        $open = (clone $query)->where('status', 'open')->count();
        $inProgress = (clone $query)->where('status', 'in_progress')->count();
        $awaitingPlayer = (clone $query)->where('status', 'awaiting_player')->count();
        $awaitingStaff = (clone $query)->where('status', 'awaiting_staff')->count();
        $resolved = (clone $query)->where('status', 'resolved')->count();
        $closed = (clone $query)->where('status', 'closed')->count();

        return [
            'total' => $all,
            'open' => $open,
            'in_progress' => $inProgress,
            'awaiting_player' => $awaitingPlayer,
            'awaiting_staff' => $awaitingStaff,
            'resolved' => $resolved,
            'closed' => $closed,
            'active' => $open + $inProgress + $awaitingPlayer + $awaitingStaff,
        ];
    }

    /**
     * Create a new support ticket
     */
    public function createTicket(User $user, array $data, array $uploadedFiles = []): SupportTicket
    {
        $settings = $this->getSettings();
        if (!$settings['enabled']) {
            throw new \RuntimeException('The Support Center is currently offline for maintenance.');
        }

        // Check max open tickets per account
        $openCount = SupportTicket::where('user_id', $user->id)
            ->whereIn('status', ['open', 'in_progress', 'awaiting_player', 'awaiting_staff'])
            ->count();

        if ($openCount >= $settings['max_open_per_account']) {
            throw new \RuntimeException("You have reached the maximum limit of {$settings['max_open_per_account']} active open tickets. Please resolve existing tickets first.");
        }

        // Verify and resolve character name if supplied
        $charName = null;
        $charId = !empty($data['char_id']) ? (int) $data['char_id'] : null;
        if ($charId && $user->account_id) {
            $characters = $this->getAccountCharacters($user->account_id);
            foreach ($characters as $c) {
                if ($c->char_id == $charId) {
                    $charName = $c->name;
                    break;
                }
            }
        }

        return DB::transaction(function () use ($user, $data, $charId, $charName, $settings, $uploadedFiles) {
            $ticket = SupportTicket::create([
                'ticket_reference' => SupportTicket::generateReference(),
                'user_id' => $user->id,
                'account_id' => $user->account_id ?? 0,
                'char_id' => $charId,
                'char_name' => $charName,
                'subject' => trim($data['subject']),
                'category' => $data['category'] ?? 'other',
                'priority' => $data['priority'] ?? $settings['default_priority'],
                'status' => 'open',
                'related_reference' => !empty($data['related_reference']) ? trim($data['related_reference']) : null,
                'map_name' => !empty($data['map_name']) ? trim($data['map_name']) : null,
                'incident_time' => !empty($data['incident_time']) ? trim($data['incident_time']) : null,
                'last_message_at' => now(),
            ]);

            // Create initial message
            $message = SupportTicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'sender_type' => 'player',
                'sender_name' => $user->username ?: $user->name,
                'message' => trim($data['message']),
                'is_internal' => false,
            ]);

            // Save attachments
            if ($settings['allow_attachments'] && !empty($uploadedFiles)) {
                $this->saveAttachments($ticket, $message, $user, $uploadedFiles, $settings);
            }

            // Record activity
            SupportTicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'activity_type' => 'created',
                'old_value' => null,
                'new_value' => 'open',
                'description' => "Ticket {$ticket->ticket_reference} submitted by player.",
            ]);

            return $ticket;
        });
    }

    /**
     * Add a reply or internal note to a ticket
     */
    public function addReply(SupportTicket $ticket, User $user, string $messageText, array $uploadedFiles = [], bool $isInternal = false): SupportTicketMessage
    {
        $settings = $this->getSettings();
        $isStaff = $user->isStaff();

        // Player reply permission checks
        if (!$isStaff) {
            if (!$settings['allow_player_replies']) {
                throw new \RuntimeException('Player replies are currently disabled by administration.');
            }
            if ($ticket->status === 'closed') {
                throw new \RuntimeException('This ticket is permanently closed. Please create a new ticket.');
            }
            if ($ticket->user_id !== $user->id) {
                throw new \RuntimeException('Unauthorized ticket access.');
            }
            $isInternal = false; // Players can never submit internal notes
        }

        return DB::transaction(function () use ($ticket, $user, $messageText, $uploadedFiles, $isInternal, $isStaff, $settings) {
            $senderType = $isStaff ? 'staff' : 'player';
            $senderName = $user->username ?: $user->name;

            $message = SupportTicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'sender_type' => $senderType,
                'sender_name' => $senderName,
                'message' => trim($messageText),
                'is_internal' => $isInternal,
            ]);

            // Handle attachments
            if ($settings['allow_attachments'] && !empty($uploadedFiles)) {
                $this->saveAttachments($ticket, $message, $user, $uploadedFiles, $settings);
            }

            // Update ticket status and timestamp (only for public messages)
            if (!$isInternal) {
                $updates = ['last_message_at' => now()];

                if ($isStaff) {
                    // Staff reply moves to awaiting player
                    $updates['status'] = 'awaiting_player';
                } else {
                    // Player reply moves to awaiting staff
                    $updates['status'] = 'awaiting_staff';
                }

                $ticket->update($updates);

                // Activity log
                SupportTicketActivity::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'activity_type' => 'reply_added',
                    'old_value' => null,
                    'new_value' => $updates['status'],
                    'description' => $isStaff ? "Support staff response posted by {$senderName}." : "Player response submitted.",
                ]);
            } else {
                // Internal staff note activity log
                SupportTicketActivity::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'activity_type' => 'internal_note',
                    'old_value' => null,
                    'new_value' => null,
                    'description' => "Internal staff note recorded by {$senderName}.",
                ]);
            }

            return $message;
        });
    }

    /**
     * Update ticket status
     */
    public function updateStatus(SupportTicket $ticket, User $user, string $newStatus, ?string $reason = null): SupportTicket
    {
        $validStatuses = ['open', 'in_progress', 'awaiting_player', 'awaiting_staff', 'resolved', 'closed'];
        if (!in_array($newStatus, $validStatuses, true)) {
            throw new \InvalidArgumentException('Invalid ticket status transition.');
        }

        $oldStatus = $ticket->status;
        if ($oldStatus === $newStatus) {
            return $ticket;
        }

        $updates = ['status' => $newStatus];
        if ($newStatus === 'resolved') {
            $updates['resolved_at'] = now();
        } elseif ($newStatus === 'closed') {
            $updates['closed_at'] = now();
        }

        $ticket->update($updates);

        $desc = "Status changed from {$oldStatus} to {$newStatus}";
        if ($reason) {
            $desc .= " (Reason: {$reason})";
        }

        SupportTicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'activity_type' => 'status_changed',
            'old_value' => $oldStatus,
            'new_value' => $newStatus,
            'description' => $desc,
        ]);

        return $ticket;
    }

    /**
     * Update ticket priority
     */
    public function updatePriority(SupportTicket $ticket, User $user, string $newPriority): SupportTicket
    {
        $valid = ['low', 'normal', 'high', 'urgent'];
        if (!in_array($newPriority, $valid, true)) {
            throw new \InvalidArgumentException('Invalid ticket priority.');
        }

        $old = $ticket->priority;
        if ($old === $newPriority) {
            return $ticket;
        }

        $ticket->update(['priority' => $newPriority]);

        SupportTicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'activity_type' => 'priority_changed',
            'old_value' => $old,
            'new_value' => $newPriority,
            'description' => "Priority adjusted from {$old} to {$newPriority}.",
        ]);

        return $ticket;
    }

    /**
     * Assign ticket to a staff member
     */
    public function assignStaff(SupportTicket $ticket, User $actor, ?int $staffId): SupportTicket
    {
        $oldStaffId = $ticket->assigned_staff_id;
        $staffUser = $staffId ? User::find($staffId) : null;

        $ticket->update([
            'assigned_staff_id' => $staffUser?->id,
            'status' => ($ticket->status === 'open' && $staffUser) ? 'in_progress' : $ticket->status,
        ]);

        $newDesc = $staffUser ? "Assigned to staff member {$staffUser->username}." : "Ticket unassigned.";

        SupportTicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $actor->id,
            'activity_type' => 'staff_assigned',
            'old_value' => (string) $oldStaffId,
            'new_value' => (string) $staffId,
            'description' => $newDesc,
        ]);

        return $ticket;
    }

    /**
     * Player closes their own ticket
     */
    public function closeTicketByPlayer(SupportTicket $ticket, User $user): SupportTicket
    {
        $settings = $this->getSettings();
        if (!$settings['allow_close']) {
            throw new \RuntimeException('Player ticket closure is disabled by the administrator.');
        }

        if ($ticket->user_id !== $user->id) {
            throw new \RuntimeException('Unauthorized ticket action.');
        }

        return $this->updateStatus($ticket, $user, 'closed', 'Closed by player.');
    }

    /**
     * Player reopens their resolved or closed ticket
     */
    public function reopenTicketByPlayer(SupportTicket $ticket, User $user): SupportTicket
    {
        $settings = $this->getSettings();
        if (!$settings['allow_reopen']) {
            throw new \RuntimeException('Ticket reopening is disabled by the administrator.');
        }

        if ($ticket->user_id !== $user->id) {
            throw new \RuntimeException('Unauthorized ticket action.');
        }

        return $this->updateStatus($ticket, $user, 'open', 'Reopened by player.');
    }

    /**
     * Query account tickets with pagination and filtering
     */
    public function getAccountTickets(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SupportTicket::where('user_id', $userId)
            ->with(['assignedStaff', 'messages'])
            ->latest('last_message_at');

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['search'])) {
            $term = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('ticket_reference', 'like', $term)
                  ->orWhere('subject', 'like', $term);
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Query all tickets for staff management with filters
     */
    public function getAllTickets(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = SupportTicket::with(['user', 'assignedStaff'])
            ->latest('last_message_at');

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['priority']) && $filters['priority'] !== 'all') {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['assigned_to'])) {
            if ($filters['assigned_to'] === 'unassigned') {
                $query->whereNull('assigned_staff_id');
            } else {
                $query->where('assigned_staff_id', (int) $filters['assigned_to']);
            }
        }

        if (!empty($filters['search'])) {
            $term = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('ticket_reference', 'like', $term)
                  ->orWhere('subject', 'like', $term)
                  ->orWhere('char_name', 'like', $term)
                  ->orWhereHas('user', function ($uq) use ($term) {
                      $uq->where('username', 'like', $term)->orWhere('email', 'like', $term);
                  });
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Store and secure file attachments
     */
    protected function saveAttachments(SupportTicket $ticket, SupportTicketMessage $message, User $user, array $files, array $settings): void
    {
        $allowedExtensions = array_map('trim', explode(',', strtolower($settings['allowed_extensions'])));
        $maxBytes = $settings['max_attachment_size_kb'] * 1024;
        $maxCount = $settings['max_attachments_per_msg'];

        $count = 0;
        foreach ($files as $file) {
            if (!($file instanceof UploadedFile) || !$file->isValid()) {
                continue;
            }

            if ($count >= $maxCount) {
                break;
            }

            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, $allowedExtensions, true)) {
                continue;
            }

            if ($file->getSize() > $maxBytes) {
                continue;
            }

            // Store inside private storage directory
            $originalName = $file->getClientOriginalName();
            $storedName = 'ticket_' . $ticket->id . '_' . Str::random(20) . '.' . $ext;
            $path = $file->storeAs('support_attachments', $storedName, 'local');

            SupportTicketAttachment::create([
                'ticket_id' => $ticket->id,
                'message_id' => $message->id,
                'user_id' => $user->id,
                'original_filename' => $originalName,
                'stored_filename' => $storedName,
                'storage_path' => $path,
                'mime_type' => $file->getClientMimeType() ?: 'application/octet-stream',
                'file_size' => $file->getSize(),
            ]);

            $count++;
        }
    }
}

