@extends('layouts.app')

@section('title', 'Ticket ' . $ticket->reference . ' - Support Center')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header Card -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-mono text-sm font-bold text-cyan-400 bg-cyan-950/60 border border-cyan-500/30 px-2.5 py-0.5 rounded-lg">
                    {{ $ticket->reference }}
                </span>
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider {{ $ticket->status_badge_class }}">
                    {{ $ticket->status_label }}
                </span>
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider {{ $ticket->priority_badge_class }}">
                    {{ ucfirst($ticket->priority) }} Priority
                </span>
                <span class="text-xs text-slate-400">
                    Category: <strong class="text-slate-200">{{ $ticket->category_label }}</strong>
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-wide mt-1">
                {{ $ticket->subject }}
            </h1>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('support.my-tickets') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-colors flex items-center gap-1.5 shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4 text-cyan-400"></i>
                <span>My Tickets</span>
            </a>

            @if($ticket->status !== 'closed' && $settings['allow_close'])
                <form action="{{ route('support.close', $ticket->reference) }}" method="POST" onsubmit="return confirm('Close this ticket? You may reopen it later if your problem persists.');">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-rose-950/80 hover:bg-rose-900 text-rose-300 border border-rose-500/30 cursor-pointer flex items-center gap-1">
                        <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                        <span>Close Ticket</span>
                    </button>
                </form>
            @elseif($ticket->status === 'closed' && $settings['allow_reopen'])
                <form action="{{ route('support.reopen', $ticket->reference) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-cyan-600 hover:bg-cyan-500 text-slate-950 cursor-pointer flex items-center gap-1 shadow">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        <span>Reopen Ticket</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-500/50 text-emerald-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0 text-emerald-400"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0 text-rose-400"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Ticket Context Details -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-2xl bg-slate-900/60 border border-slate-800 text-xs">
        <div>
            <span class="text-slate-500 block text-[10px] uppercase font-semibold">Submitted</span>
            <span class="font-mono text-slate-200">{{ $ticket->created_at->format('M d, Y H:i') }}</span>
        </div>
        <div>
            <span class="text-slate-500 block text-[10px] uppercase font-semibold">Last Activity</span>
            <span class="font-mono text-slate-200">{{ $ticket->last_activity_at ? $ticket->last_activity_at->diffForHumans() : $ticket->created_at->diffForHumans() }}</span>
        </div>
        <div>
            <span class="text-slate-500 block text-[10px] uppercase font-semibold">Character Involved</span>
            <span class="font-semibold text-slate-200">{{ $ticket->character_name ?: 'None' }}</span>
        </div>
        <div>
            <span class="text-slate-500 block text-[10px] uppercase font-semibold">Handling Staff</span>
            <span class="font-semibold text-cyan-400">{{ $ticket->assignedStaff ? $ticket->assignedStaff->username : 'Support Team' }}</span>
        </div>
    </div>

    <!-- Conversation Thread -->
    <div class="space-y-4">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2 px-1">
            <i data-lucide="messages-square" class="w-4 h-4 text-cyan-400"></i>
            <span>Conversation Thread</span>
        </h3>

        @foreach($ticket->publicMessages as $msg)
            <div class="p-5 rounded-2xl {{ $msg->is_staff ? 'bg-cyan-950/30 border border-cyan-500/30 shadow-lg' : 'bg-slate-900/80 border border-slate-800' }} space-y-3">
                <!-- Message Author & Timestamp -->
                <div class="flex items-center justify-between pb-3 border-b {{ $msg->is_staff ? 'border-cyan-500/20' : 'border-slate-800' }}">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs {{ $msg->is_staff ? 'bg-cyan-500 text-slate-950' : 'bg-slate-800 text-slate-300' }}">
                            {{ strtoupper(substr($msg->user->username ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold {{ $msg->is_staff ? 'text-cyan-300' : 'text-white' }}">
                                    {{ $msg->user->username ?? 'Player' }}
                                </span>
                                @if($msg->is_staff)
                                    <span class="px-2 py-0.2 rounded text-[9px] font-mono font-bold uppercase tracking-wider bg-cyan-900/80 text-cyan-300 border border-cyan-400/40">
                                        Staff Member
                                    </span>
                                @endif
                            </div>
                            <span class="text-[10px] text-slate-500 font-mono">{{ $msg->created_at->format('M d, Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Message Body -->
                <div class="text-xs text-slate-200 leading-relaxed whitespace-pre-wrap">
                    {{ $msg->message }}
                </div>

                <!-- Attachments -->
                @if($msg->attachments->count() > 0)
                    <div class="pt-2 border-t {{ $msg->is_staff ? 'border-cyan-500/20' : 'border-slate-800' }} space-y-1.5">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Attached Files:</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach($msg->attachments as $att)
                                <a href="{{ route('support.attachment', [$ticket->reference, $att->id]) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-mono {{ $msg->is_staff ? 'bg-cyan-950/60 border border-cyan-500/40 text-cyan-300' : 'bg-slate-950 border border-slate-800 text-slate-300' }} hover:underline">
                                    <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                                    <span>{{ $att->original_filename }} ({{ $att->formatted_size }})</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Reply Box -->
    @if($ticket->status === 'closed')
        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 text-center space-y-2">
            <i data-lucide="lock" class="w-8 h-8 text-slate-500 mx-auto"></i>
            <h4 class="text-sm font-bold text-white">This ticket is currently closed</h4>
            <p class="text-xs text-slate-400">Replies are disabled for closed tickets. If your problem has not been solved, reopen this ticket above.</p>
        </div>
    @elseif(!$settings['allow_player_replies'])
        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 text-center space-y-2">
            <i data-lucide="alert-circle" class="w-8 h-8 text-amber-400 mx-auto"></i>
            <h4 class="text-sm font-bold text-white">Player replies are temporarily paused</h4>
            <p class="text-xs text-slate-400">Our team is currently reviewing your case. We will contact you once further information is ready.</p>
        </div>
    @else
        <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-6 shadow-xl space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-800">
                <i data-lucide="reply" class="w-5 h-5 text-cyan-400"></i>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Post a Reply</h3>
            </div>

            <form action="{{ route('support.reply', $ticket->reference) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <textarea name="message" rows="4" required placeholder="Type your response or provide requested details..."
                              class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 leading-relaxed transition-all">{{ old('message') }}</textarea>
                </div>

                @if($settings['allow_attachments'])
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Add Attachments (Optional)</label>
                        <input type="file" name="attachments[]" multiple
                               class="block w-full text-xs text-slate-400 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-cyan-400 hover:file:bg-slate-700 cursor-pointer">
                    </div>
                @endif

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 hover:from-cyan-400 hover:to-blue-500 shadow-lg shadow-cyan-500/20 cursor-pointer flex items-center gap-2">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Send Reply</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

</div>
@endsection
