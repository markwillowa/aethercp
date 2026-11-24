@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->ticket_reference)

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <!-- Top Wood Board Header -->
    <div class="chronicles-wood-board flex flex-col md:flex-row md:items-center justify-between gap-6 p-6 shadow-2xl">
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('support.my-tickets') }}" class="text-xs text-[#D4AF37] hover:text-[#FFE4A0] font-cinzel font-bold flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>All My Tickets</span>
                </a>
                <span class="text-slate-500">•</span>
                <span class="text-xs font-mono font-bold text-[#FFE894]">{{ $ticket->ticket_reference }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black font-cinzel text-[#FCE69B] tracking-wider uppercase mt-1">
                {{ $ticket->subject }}
            </h1>
            <div class="flex items-center gap-2 mt-2 flex-wrap text-xs">
                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono border {{ $ticket->status_badge_class }}">
                    {{ $ticket->status_label }}
                </span>
                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono border {{ $ticket->priority_badge_class }}">
                    {{ $ticket->priority_label }}
                </span>
                <span class="px-2.5 py-0.5 rounded text-[10px] bg-[#140E1B] text-[#D8CDBC] border border-[#5A456B]">
                    {{ $ticket->category_label }}
                </span>
            </div>
        </div>

        <!-- Ticket Actions -->
        <div class="flex items-center gap-2 flex-wrap">
            @if($ticket->isOpen() && $settings['allow_close'])
                <form action="{{ route('support.close', $ticket->ticket_reference) }}" method="POST" onsubmit="return confirm('Are you sure you want to close this support ticket?');">
                    @csrf
                    <button type="submit" class="chronicles-btn-dark py-2 px-4 cursor-pointer text-xs font-cinzel font-bold flex items-center gap-1.5">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-400"></i>
                        <span>Mark Resolved &amp; Close</span>
                    </button>
                </form>
            @elseif($ticket->isClosed() && $settings['allow_reopen'])
                <form action="{{ route('support.reopen', $ticket->ticket_reference) }}" method="POST" onsubmit="return confirm('Reopen this support ticket for further assistance?');">
                    @csrf
                    <button type="submit" class="chronicles-btn-gold py-2 px-4 cursor-pointer text-xs font-cinzel font-bold flex items-center gap-1.5 shadow-md">
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

    <!-- Ticket Metadata Panel -->
    <div class="chronicles-panel p-4" style="background: #181120; border: 1.5px solid #4A3A5A;">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#A898B5] font-cinzel block mb-0.5">Submitted On</span>
                <span class="font-mono text-[#EDE4D2]">{{ $ticket->created_at->format('M d, Y H:i') }}</span>
            </div>

            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#A898B5] font-cinzel block mb-0.5">Character Involved</span>
                <span class="font-semibold text-[#FFE4A0]">{{ $ticket->char_name ?: 'None / Account-Wide' }}</span>
            </div>

            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#A898B5] font-cinzel block mb-0.5">Assigned Staff</span>
                @if($ticket->assignedStaff)
                    <span class="font-semibold text-[#70E0D0] flex items-center gap-1">
                        <i data-lucide="shield" class="w-3 h-3"></i>
                        <span>{{ $ticket->assignedStaff->username }}</span>
                    </span>
                @else
                    <span class="text-[#8C7B99] italic">Support Team Queue</span>
                @endif
            </div>

            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#A898B5] font-cinzel block mb-0.5">Last Activity</span>
                <span class="font-mono text-[#EDE4D2]">{{ $ticket->last_message_at->diffForHumans() }}</span>
            </div>
        </div>

        @if($ticket->related_reference || $ticket->map_name || $ticket->incident_time)
            <div class="mt-3 pt-3 border-t border-[#2F213C] flex items-center gap-4 text-[11px] text-[#A898B5] flex-wrap">
                @if($ticket->related_reference)
                    <span>Reference: <strong class="text-[#EDE4D2]">{{ $ticket->related_reference }}</strong></span>
                @endif
                @if($ticket->map_name)
                    <span>Map: <strong class="text-[#EDE4D2]">{{ $ticket->map_name }}</strong></span>
                @endif
                @if($ticket->incident_time)
                    <span>Incident: <strong class="text-[#EDE4D2]">{{ $ticket->incident_time }}</strong></span>
                @endif
            </div>
        @endif
    </div>

    <!-- Conversation Messaging Thread -->
    <div class="space-y-4">
        <h3 class="font-cinzel text-sm font-bold text-[#F3E5AB] uppercase tracking-wide flex items-center gap-2">
            <i data-lucide="message-square" class="w-4 h-4 text-[#D4AF37]"></i>
            <span>Conversation Thread</span>
        </h3>

        <div class="space-y-4">
            @foreach($ticket->messages as $msg)
                @php
                    $isStaff = ($msg->sender_type === 'staff');
                @endphp

                <div class="p-5 rounded-2xl transition-all shadow-md {{ $isStaff ? 'bg-[#22162E] border-2 border-[#806442] shadow-[0_4px_20px_rgba(212,175,55,0.1)]' : 'bg-[#160E1E] border border-[#4A3A5A]' }}">
                    <!-- Sender Header -->
                    <div class="flex items-center justify-between pb-3 border-b {{ $isStaff ? 'border-[#4A3A5A]' : 'border-[#2F213C]' }}">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 {{ $isStaff ? 'bg-[#D4AF37]/20 border border-[#D4AF37] text-[#FFD700]' : 'bg-[#251A33] border border-[#5A456B] text-[#EDE4D2]' }}">
                                @if($isStaff)
                                    <i data-lucide="shield" class="w-4 h-4"></i>
                                @else
                                    <i data-lucide="user" class="w-4 h-4"></i>
                                @endif
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-cinzel font-bold text-xs {{ $isStaff ? 'text-[#FFD700]' : 'text-white' }}">{{ $msg->sender_name }}</span>
                                    @if($isStaff)
                                        <span class="px-2 py-0.2 rounded text-[9px] font-bold font-cinzel uppercase bg-[#3E2918] text-[#FFE894] border border-[#8C6E3D]">
                                            Support Staff
                                        </span>
                                    @else
                                        <span class="px-2 py-0.2 rounded text-[9px] font-bold font-mono bg-[#251A33] text-[#A898B5] border border-[#4A3A5A]">
                                            Adventurer
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <span class="text-[10px] text-[#8C7B99] font-mono">
                            {{ $msg->created_at->format('M d, Y H:i') }}
                        </span>
                    </div>

                    <!-- Message Body -->
                    <div class="pt-3.5 text-xs text-[#EDE4D2] leading-relaxed whitespace-pre-wrap font-sans">
                        {{ $msg->message }}
                    </div>

                    <!-- Message Attachments -->
                    @if($msg->attachments->count() > 0)
                        <div class="mt-4 pt-3 border-t {{ $isStaff ? 'border-[#4A3A5A]' : 'border-[#2F213C]' }} space-y-2">
                            <span class="text-[10px] font-bold text-[#A898B5] font-cinzel uppercase tracking-wider block">Attached Files:</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($msg->attachments as $att)
                                    <a href="{{ route('support.attachment', ['reference' => $ticket->ticket_reference, 'attachmentId' => $att->id]) }}" 
                                       class="px-3 py-1.5 rounded-lg text-xs bg-[#120B18] border border-[#4A3A5A] hover:border-[#D4AF37] hover:text-[#FFD700] text-[#EDE4D2] flex items-center gap-2 transition-colors">
                                        <i data-lucide="{{ $att->is_image ? 'image' : 'file-text' }}" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                                        <span class="font-medium truncate max-w-[180px]">{{ $att->original_filename }}</span>
                                        <span class="text-[10px] text-[#8C7B99] font-mono">({{ $att->formatted_size }})</span>
                                        <i data-lucide="download" class="w-3 h-3 ml-1 text-[#8C7B99]"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Reply Box -->
    @if($ticket->isOpen() && $settings['allow_player_replies'])
        <div class="chronicles-panel p-6" style="background: linear-gradient(180deg, #1C1525 0%, #150F1D 100%); border: 2px solid #5A456B;">
            <h4 class="font-cinzel text-xs font-bold text-[#FFD700] uppercase tracking-wide flex items-center gap-2 mb-3">
                <i data-lucide="reply" class="w-4 h-4 text-[#D4AF37]"></i>
                <span>Post a Reply</span>
            </h4>

            <form action="{{ route('support.reply', $ticket->ticket_reference) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <textarea name="message" rows="4" required placeholder="Type your response to support staff here..." 
                              class="w-full p-4 bg-[#100B17] border border-[#6B537F] rounded-xl text-xs font-medium text-[#F3E5AB] placeholder:text-[#8D7D9A] focus:outline-none focus:border-[#D4AF37] focus:ring-1 focus:ring-[#D4AF37] transition-all leading-relaxed"></textarea>
                </div>

                @if($settings['allow_attachments'])
                    <div class="p-3.5 rounded-xl bg-[#120B18] border border-[#3E2E4E] space-y-1.5">
                        <label class="block text-[11px] font-bold text-[#C8BDAA] font-cinzel flex items-center gap-1.5">
                            <i data-lucide="paperclip" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                            <span>Attach Additional Screenshots or Logs</span>
                        </label>
                        <input type="file" name="attachments[]" multiple 
                               class="block w-full text-xs text-[#C8BDAA] file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-cinzel file:font-bold file:bg-[#2A1D36] file:text-[#FFE894] hover:file:bg-[#382647] cursor-pointer">
                        <p class="text-[10px] text-[#8C7B99]">
                            Allowed: {{ strtoupper($settings['allowed_extensions']) }} (Max {{ number_format($settings['max_attachment_size_kb'] / 1024, 1) }} MB)
                        </p>
                    </div>
                @endif

                <div class="flex justify-end pt-2">
                    <button type="submit" class="chronicles-btn-gold py-2.5 px-6 cursor-pointer text-xs font-cinzel font-black tracking-wide shadow-md flex items-center gap-2">
                        <i data-lucide="send" class="w-3.5 h-3.5"></i>
                        <span>SEND REPLY</span>
                    </button>
                </div>
            </form>
        </div>
    @elseif($ticket->isClosed())
        <div class="p-6 rounded-2xl bg-[#140E1B] border border-[#3E2E4E] text-center space-y-3">
            <div class="w-12 h-12 rounded-full bg-[#241A2F] border border-[#5A456B] flex items-center justify-center mx-auto text-slate-400">
                <i data-lucide="lock" class="w-6 h-6"></i>
            </div>
            <div>
                <h4 class="font-cinzel text-sm font-bold text-[#F3E5AB]">This Support Case is Closed</h4>
                <p class="text-xs text-[#A898B5] mt-1">This ticket has been marked as resolved or closed. If you need further assistance, you may reopen it or submit a new inquiry.</p>
            </div>
            @if($settings['allow_reopen'])
                <form action="{{ route('support.reopen', $ticket->ticket_reference) }}" method="POST">
                    @csrf
                    <button type="submit" class="chronicles-btn-gold py-2 px-5 text-xs font-cinzel font-bold inline-flex items-center gap-2 shadow-md">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        <span>Reopen This Ticket</span>
                    </button>
                </form>
            @endif
        </div>
    @endif
</div>
@endsection
