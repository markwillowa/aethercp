<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketAttachment;
use App\Models\User;
use App\Services\Support\SupportTicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminSupportController extends Controller
{
    public function __construct(
        protected SupportTicketService $supportService
    ) {}

    /**
     * Admin Ticket Management & Dashboard
     */
    public function index(Request $request): View
    {
        $stats = $this->supportService->getStatistics();
        $tickets = $this->supportService->getAllTickets($request->all(), 20);
        $staffMembers = User::whereIn('role', ['admin', 'staff'])->orderBy('username')->get();
        $settings = $this->supportService->getSettings();

        return view('admin.support.index', compact('stats', 'tickets', 'staffMembers', 'settings'));
    }

    /**
     * Admin Ticket Detail, Full Conversation, Notes & Actions
     */
    public function show(int $id): View
    {
        $ticket = SupportTicket::with([
            'user',
            'assignedStaff',
            'messages.attachments',
            'activities.user',
        ])->findOrFail($id);

        $staffMembers = User::whereIn('role', ['admin', 'staff'])->orderBy('username')->get();
        $settings = $this->supportService->getSettings();

        return view('admin.support.show', compact('ticket', 'staffMembers', 'settings'));
    }

    /**
     * Post Staff Reply or Internal Note
     */
    public function reply(Request $request, int $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $settings = $this->supportService->getSettings();

        $request->validate([
            'message' => ['required', 'string', 'min:2'],
            'is_internal' => ['nullable', 'boolean'],
            'attachments.*' => ['nullable', 'file', 'max:' . $settings['max_attachment_size_kb']],
        ]);

        $isInternal = (bool) $request->input('is_internal', false);

        try {
            $this->supportService->addReply(
                $ticket,
                auth()->user(),
                $request->input('message'),
                $request->file('attachments') ?? [],
                $isInternal
            );

            $msg = $isInternal ? 'Internal staff note added to ticket.' : 'Official staff reply posted to player.';
            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update Ticket Status, Priority, or Staff Assignment
     */
    public function update(Request $request, int $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $user = auth()->user();

        // 1. Status Update
        if ($request->has('status') && $request->input('status') !== $ticket->status) {
            $this->supportService->updateStatus($ticket, $user, $request->input('status'), $request->input('reason'));
        }

        // 2. Priority Update
        if ($request->has('priority') && $request->input('priority') !== $ticket->priority) {
            $this->supportService->updatePriority($ticket, $user, $request->input('priority'));
        }

        // 3. Staff Assignment
        if ($request->has('assigned_staff_id')) {
            $staffId = $request->input('assigned_staff_id') ? (int) $request->input('assigned_staff_id') : null;
            if ($staffId !== $ticket->assigned_staff_id) {
                $this->supportService->assignStaff($ticket, $user, $staffId);
            }
        }

        return back()->with('success', "Ticket #{$ticket->ticket_reference} updated successfully.");
    }

    /**
     * Support Center Configuration View
     */
    public function settings(): View
    {
        $settings = $this->supportService->getSettings();
        return view('admin.support.settings', compact('settings'));
    }

    /**
     * Save Support Configuration
     */
    public function updateSettings(Request $request)
    {
        $this->supportService->updateSettings($request->all());
        return back()->with('success', 'Support Center configuration saved successfully.');
    }

    /**
     * Admin Secure Attachment Download
     */
    public function downloadAttachment(int $id, int $attachmentId)
    {
        $ticket = SupportTicket::findOrFail($id);
        $attachment = SupportTicketAttachment::where('ticket_id', $ticket->id)
            ->where('id', $attachmentId)
            ->firstOrFail();

        if (!Storage::disk('local')->exists($attachment->storage_path)) {
            abort(404, 'Attachment file not found on server storage.');
        }

        return Storage::disk('local')->download($attachment->storage_path, $attachment->original_filename);
    }
}
