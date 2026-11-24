<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketAttachment;
use App\Services\Support\SupportTicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SupportController extends Controller
{
    public function __construct(
        protected SupportTicketService $supportService
    ) {}

    /**
     * Support Center Landing Page
     */
    public function index(): View
    {
        $user = auth()->user();
        $settings = $this->supportService->getSettings();
        $stats = $this->supportService->getStatistics($user->id);
        $recentTickets = SupportTicket::where('user_id', $user->id)
            ->with(['assignedStaff'])
            ->latest('last_message_at')
            ->take(5)
            ->get();

        return view('support.index', compact('user', 'settings', 'stats', 'recentTickets'));
    }

    /**
     * My Tickets List with Filtering
     */
    public function myTickets(Request $request): View
    {
        $user = auth()->user();
        $settings = $this->supportService->getSettings();
        $stats = $this->supportService->getStatistics($user->id);
        $tickets = $this->supportService->getAccountTickets($user->id, $request->all(), 12);

        return view('support.my-tickets', compact('user', 'settings', 'stats', 'tickets'));
    }

    /**
     * Submit Ticket Form
     */
    public function create(): View
    {
        $user = auth()->user();
        $settings = $this->supportService->getSettings();

        if (!$settings['enabled']) {
            return redirect()->route('support.index')->with('error', 'The Support Center is currently closed for new tickets.');
        }

        $characters = $user->account_id ? $this->supportService->getAccountCharacters($user->account_id) : [];

        return view('support.create', compact('user', 'settings', 'characters'));
    }

    /**
     * Store new support ticket
     */
    public function store(Request $request)
    {
        $settings = $this->supportService->getSettings();
        if (!$settings['enabled']) {
            return redirect()->route('support.index')->with('error', 'Ticket submissions are currently disabled.');
        }

        $request->validate([
            'subject' => ['required', 'string', 'min:5', 'max:200'],
            'category' => ['required', 'string'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'message' => ['required', 'string', 'min:10'],
            'char_id' => ['nullable', 'integer'],
            'related_reference' => ['nullable', 'string', 'max:100'],
            'map_name' => ['nullable', 'string', 'max:50'],
            'incident_time' => ['nullable', 'string', 'max:50'],
            'attachments.*' => ['nullable', 'file', 'max:' . $settings['max_attachment_size_kb']],
        ]);

        try {
            $ticket = $this->supportService->createTicket(
                auth()->user(),
                $request->all(),
                $request->file('attachments') ?? []
            );

            return redirect()->route('support.show', $ticket->ticket_reference)
                ->with('success', "Your support ticket #{$ticket->ticket_reference} has been submitted successfully.");
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Ticket Detail & Conversation View
     */
    public function show(string $reference): View
    {
        $user = auth()->user();
        $ticket = SupportTicket::where('ticket_reference', $reference)
            ->where('user_id', $user->id)
            ->with([
                'assignedStaff',
                'messages' => function ($q) {
                    $q->where('is_internal', false)->with('attachments');
                },
                'activities',
            ])
            ->firstOrFail();

        $settings = $this->supportService->getSettings();

        return view('support.show', compact('user', 'ticket', 'settings'));
    }

    /**
     * Player reply to existing ticket
     */
    public function reply(Request $request, string $reference)
    {
        $user = auth()->user();
        $ticket = SupportTicket::where('ticket_reference', $reference)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $settings = $this->supportService->getSettings();

        $request->validate([
            'message' => ['required', 'string', 'min:2'],
            'attachments.*' => ['nullable', 'file', 'max:' . $settings['max_attachment_size_kb']],
        ]);

        try {
            $this->supportService->addReply(
                $ticket,
                $user,
                $request->input('message'),
                $request->file('attachments') ?? []
            );

            return redirect()->route('support.show', $ticket->ticket_reference)
                ->with('success', 'Your reply has been posted to the support ticket.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Player closes ticket
     */
    public function close(string $reference)
    {
        $user = auth()->user();
        $ticket = SupportTicket::where('ticket_reference', $reference)
            ->where('user_id', $user->id)
            ->firstOrFail();

        try {
            $this->supportService->closeTicketByPlayer($ticket, $user);
            return back()->with('success', "Ticket #{$ticket->ticket_reference} has been marked as closed.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Player reopens ticket
     */
    public function reopen(string $reference)
    {
        $user = auth()->user();
        $ticket = SupportTicket::where('ticket_reference', $reference)
            ->where('user_id', $user->id)
            ->firstOrFail();

        try {
            $this->supportService->reopenTicketByPlayer($ticket, $user);
            return back()->with('success', "Ticket #{$ticket->ticket_reference} has been reopened.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Secure Attachment Download
     */
    public function downloadAttachment(string $reference, int $attachmentId)
    {
        $user = auth()->user();
        $ticket = SupportTicket::where('ticket_reference', $reference)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $attachment = SupportTicketAttachment::where('ticket_id', $ticket->id)
            ->where('id', $attachmentId)
            ->firstOrFail();

        if (!Storage::disk('local')->exists($attachment->storage_path)) {
            abort(404, 'Attachment file not found on server storage.');
        }

        return Storage::disk('local')->download($attachment->storage_path, $attachment->original_filename);
    }
}
