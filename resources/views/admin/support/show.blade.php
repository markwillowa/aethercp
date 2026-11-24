@extends('layouts.admin')

@section('title', 'Manage Ticket #' . $ticket->ticket_reference)

@section('admin_content')
<div class="space-y-6" x-data="{ isInternalNote: false }">

    <!-- Top Navigation & Summary Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-[#1C1425] border border-[#4A3A5A] shadow-xl">
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('admin.support.index') }}" class="text-xs text-[#D4AF37] hover:text-[#FFE4A0] font-cinzel font-bold flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>All Tickets</span>
                </a>
                <span class="text-slate-500">•</span>
                <span class="text-xs font-mono font-bold text-[#FFE894]">{{ $ticket->ticket_reference }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-white font-cinzel tracking-wide uppercase mt-1">
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

        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-400">Assigned:</span>
            <span class="font-bold text-cyan-300 font-cinzel">
                {{ $ticket->assignedStaff?->username ?? 'Unassigned' }}
            </span>
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

    <!-- Main Content: Left Conversation & Right Case Controls -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Conversation & Reply Box (2 Columns on large screens) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Conversation Messages -->
            <div class="space-y-4">
                <h3 class="font-cinzel text-sm font-bold text-[#F3E5AB] uppercase tracking-wide flex items-center gap-2">
                    <i data-lucide="message-square" class="w-4 h-4 text-[#D4AF37]"></i>
                    <span>Ticket Conversation &amp; Internal Notes</span>
                </h3>

                <div class="space-y-4">
                    @foreach($ticket->messages as $msg)
                        @php
                            $isInternal = $msg->is_internal;
                            $isStaff = ($msg->sender_type === 'staff');
                        @endphp

                        <div class="p-5 rounded-2xl transition-all shadow-md {{ $isInternal ? 'bg-[#2D2115] border-2 border-amber-500/60 shadow-[0_4px_20px_rgba(245,158,11,0.15)]' : ($isStaff ? 'bg-[#22162E] border-2 border-[#806442]' : 'bg-[#160E1E] border border-[#4A3A5A]') }}">
                            <!-- Sender Header -->
                            <div class="flex items-center justify-between pb-3 border-b {{ $isInternal ? 'border-amber-500/30' : ($isStaff ? 'border-[#4A3A5A]' : 'border-[#2F213C]') }}">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 {{ $isInternal ? 'bg-amber-500/20 border border-amber-500 text-amber-300' : ($isStaff ? 'bg-[#D4AF37]/20 border border-[#D4AF37] text-[#FFD700]' : 'bg-[#251A33] border border-[#5A456B] text-[#EDE4D2]') }}">
                                        @if($isInternal)
                                            <i data-lucide="lock" class="w-4 h-4"></i>
                                        @elseif($isStaff)
                                            <i data-lucide="shield" class="w-4 h-4"></i>
                                        @else
                                            <i data-lucide="user" class="w-4 h-4"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-cinzel font-bold text-xs {{ $isInternal ? 'text-amber-300' : ($isStaff ? 'text-[#FFD700]' : 'text-white') }}">{{ $msg->sender_name }}</span>
                                            @if($isInternal)
                                                <span class="px-2 py-0.2 rounded text-[9px] font-bold font-mono uppercase bg-amber-500/20 text-amber-300 border border-amber-500/40">
                                                    INTERNAL NOTE • STAFF ONLY
                                                </span>
                                            @elseif($isStaff)
                                                <span class="px-2 py-0.2 rounded text-[9px] font-bold font-cinzel uppercase bg-[#3E2918] text-[#FFE894] border border-[#8C6E3D]">
                                                    Support Staff
                                                </span>
                                            @else
                                                <span class="px-2 py-0.2 rounded text-[9px] font-bold font-mono bg-[#251A33] text-[#A898B5] border border-[#4A3A5A]">
                                                    Player
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <span class="text-[10px] text-slate-400 font-mono">
                                    {{ $msg->created_at->format('M d, Y H:i:s') }}
                                </span>
                            </div>

                            <!-- Message Content -->
                            <div class="pt-3.5 text-xs text-[#EDE4D2] leading-relaxed whitespace-pre-wrap font-sans">
                                {{ $msg->message }}
                            </div>

                            <!-- Attachments -->
                            @if($msg->attachments->count() > 0)
                                <div class="mt-4 pt-3 border-t {{ $isInternal ? 'border-amber-500/30' : ($isStaff ? 'border-[#4A3A5A]' : 'border-[#2F213C]') }} space-y-2">
                                    <span class="text-[10px] font-bold text-[#A898B5] font-cinzel uppercase tracking-wider block">Attachments:</span>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($msg->attachments as $att)
                                            <a href="{{ route('admin.support.attachment', ['id' => $ticket->id, 'attachmentId' => $att->id]) }}" 
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

            <!-- Staff Response & Internal Note Box -->
            <div class="rounded-2xl p-6 transition-all border-2" :class="isInternalNote ? 'bg-[#2D2115] border-amber-500/70 shadow-lg' : 'bg-[#1C1425] border-[#5A456B] shadow-xl'">
                <div class="flex items-center justify-between mb-3 pb-2 border-b" :class="isInternalNote ? 'border-amber-500/30' : 'border-[#3E2F4D]'">
                    <h4 class="font-cinzel text-xs font-bold uppercase tracking-wide flex items-center gap-2" :class="isInternalNote ? 'text-amber-300' : 'text-[#FFD700]'">
                        <i data-lucide="edit-3" class="w-4 h-4" :class="isInternalNote ? 'text-amber-400' : 'text-[#D4AF37]'"></i>
                        <span x-text="isInternalNote ? 'Add Internal Staff Note (Confidential)' : 'Post Staff Response to Player'"></span>
                    </h4>

                    <!-- Note Mode Checkbox Toggle -->
                    <label class="flex items-center gap-2 text-xs cursor-pointer select-none px-3 py-1 rounded-lg border transition-colors" :class="isInternalNote ? 'bg-amber-500/20 border-amber-500/50 text-amber-300' : 'bg-[#140E1B] border-[#4A3A5A] text-slate-300 hover:text-white'">
                        <input type="checkbox" name="is_internal" x-model="isInternalNote" class="rounded text-amber-500 focus:ring-0">
                        <span class="font-bold font-cinzel">Internal Note Mode</span>
                    </label>
                </div>

                <form action="{{ route('admin.support.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="is_internal" :value="isInternalNote ? 1 : 0">

                    <div>
                        <textarea name="message" rows="4" required :placeholder="isInternalNote ? 'Record confidential notes for other administrators and GMs (never visible to the player)...' : 'Type your official support response to the player...'" 
                                  class="w-full p-4 bg-[#100B17] border rounded-xl text-xs font-medium text-[#F3E5AB] placeholder:text-[#8D7D9A] focus:outline-none transition-all leading-relaxed"
                                  :class="isInternalNote ? 'border-amber-500/60 focus:border-amber-400' : 'border-[#6B537F] focus:border-[#D4AF37]'"></textarea>
                    </div>

                    <!-- File Attachments -->
                    @if($settings['allow_attachments'])
                        <div class="p-3.5 rounded-xl bg-[#120B18] border border-[#3E2F4D] space-y-1.5">
                            <label class="block text-[11px] font-bold text-[#C8BDAA] font-cinzel flex items-center gap-1.5">
                                <i data-lucide="paperclip" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                                <span>Attach Staff Documentation / Proof Files</span>
                            </label>
                            <input type="file" name="attachments[]" multiple 
                                   class="block w-full text-xs text-[#C8BDAA] file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-cinzel file:font-bold file:bg-[#2A1D36] file:text-[#FFE894] hover:file:bg-[#382647] cursor-pointer">
                        </div>
                    @endif

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="py-2.5 px-6 rounded-xl text-xs font-cinzel font-black tracking-wide shadow-md flex items-center gap-2 transition-transform hover:scale-105" :class="isInternalNote ? 'bg-amber-500 hover:bg-amber-400 text-black' : 'bg-[#D4AF37] hover:bg-[#FFE4A0] text-black'">
                            <i data-lucide="send" class="w-3.5 h-3.5"></i>
                            <span x-text="isInternalNote ? 'RECORD INTERNAL NOTE' : 'POST STAFF REPLY'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Case Controls & Audit Trail (1 Column) -->
        <div class="space-y-6">

            <!-- Case Action Controls -->
            <div class="p-5 rounded-2xl bg-[#1C1425] border border-[#4A3A5A] shadow-xl space-y-4">
                <h3 class="font-cinzel text-xs font-bold text-[#FFD700] uppercase tracking-wide flex items-center gap-2 pb-2 border-b border-[#3E2F4D]">
                    <i data-lucide="sliders" class="w-4 h-4 text-[#D4AF37]"></i>
                    <span>Ticket Management</span>
                </h3>

                <!-- Update Status Form -->
                <form action="{{ route('admin.support.update', $ticket->id) }}" method="POST" class="space-y-2">
                    @csrf
                    <label class="block text-[11px] font-bold text-slate-300 font-cinzel">Case Status</label>
                    <div class="flex items-center gap-2">
                        <select name="status" class="flex-1 px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#5A456B] text-white focus:outline-none focus:border-[#D4AF37] cursor-pointer">
                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="awaiting_player" {{ $ticket->status === 'awaiting_player' ? 'selected' : '' }}>Awaiting Player</option>
                            <option value="awaiting_staff" {{ $ticket->status === 'awaiting_staff' ? 'selected' : '' }}>Awaiting Staff</option>
                            <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                        <button type="submit" class="px-3 py-2 rounded-xl text-xs font-cinzel font-bold bg-[#140E1B] border border-[#5A456B] text-[#FFE894] hover:bg-[#251A33] transition-colors">
                            Update
                        </button>
                    </div>
                </form>

                <!-- Update Priority Form -->
                <form action="{{ route('admin.support.update', $ticket->id) }}" method="POST" class="space-y-2">
                    @csrf
                    <label class="block text-[11px] font-bold text-slate-300 font-cinzel">Priority Level</label>
                    <div class="flex items-center gap-2">
                        <select name="priority" class="flex-1 px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#5A456B] text-white focus:outline-none focus:border-[#D4AF37] cursor-pointer">
                            <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="normal" {{ $ticket->priority === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        <button type="submit" class="px-3 py-2 rounded-xl text-xs font-cinzel font-bold bg-[#140E1B] border border-[#5A456B] text-[#FFE894] hover:bg-[#251A33] transition-colors">
                            Update
                        </button>
                    </div>
                </form>

                <!-- Assign Staff Form -->
                <form action="{{ route('admin.support.update', $ticket->id) }}" method="POST" class="space-y-2">
                    @csrf
                    <label class="block text-[11px] font-bold text-slate-300 font-cinzel">Assigned Staff</label>
                    <div class="flex items-center gap-2">
                        <select name="assigned_staff_id" class="flex-1 px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#5A456B] text-white focus:outline-none focus:border-[#D4AF37] cursor-pointer">
                            <option value="">Unassigned</option>
                            @foreach($staffMembers as $sMember)
                                <option value="{{ $sMember->id }}" {{ $ticket->assigned_staff_id == $sMember->id ? 'selected' : '' }}>
                                    {{ $sMember->username }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-3 py-2 rounded-xl text-xs font-cinzel font-bold bg-[#140E1B] border border-[#5A456B] text-[#FFE894] hover:bg-[#251A33] transition-colors">
                            Assign
                        </button>
                    </div>
                </form>
            </div>

            <!-- Player & Ticket Details Card -->
            <div class="p-5 rounded-2xl bg-[#1C1425] border border-[#4A3A5A] shadow-xl space-y-3 text-xs">
                <h3 class="font-cinzel text-xs font-bold text-[#FFD700] uppercase tracking-wide pb-2 border-b border-[#3E2F4D]">
                    Player &amp; Case Specs
                </h3>

                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Player:</span>
                        <span class="font-bold text-white">{{ $ticket->user->username ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Email:</span>
                        <span class="font-mono text-slate-300">{{ $ticket->user->email ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Account ID:</span>
                        <span class="font-mono text-white">{{ $ticket->account_id }}</span>
                    </div>
                    @if($ticket->char_name)
                        <div class="flex justify-between">
                            <span class="text-slate-400">Character:</span>
                            <span class="font-bold text-amber-300">{{ $ticket->char_name }} (ID #{{ $ticket->char_id }})</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-slate-400">Category:</span>
                        <span class="text-slate-300">{{ $ticket->category_label }}</span>
                    </div>
                    @if($ticket->map_name)
                        <div class="flex justify-between">
                            <span class="text-slate-400">Map Name:</span>
                            <span class="font-mono text-slate-300">{{ $ticket->map_name }}</span>
                        </div>
                    @endif
                    @if($ticket->incident_time)
                        <div class="flex justify-between">
                            <span class="text-slate-400">Incident Time:</span>
                            <span class="text-slate-300">{{ $ticket->incident_time }}</span>
                        </div>
                    @endif
                    @if($ticket->related_reference)
                        <div class="flex justify-between">
                            <span class="text-slate-400">Related Ref:</span>
                            <span class="font-mono text-slate-300">{{ $ticket->related_reference }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between pt-2 border-t border-[#3E2F4D]">
                        <span class="text-slate-400">Created:</span>
                        <span class="font-mono text-slate-300">{{ $ticket->created_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Activity Audit Trail -->
            <div class="p-5 rounded-2xl bg-[#1C1425] border border-[#4A3A5A] shadow-xl space-y-3">
                <h3 class="font-cinzel text-xs font-bold text-[#FFD700] uppercase tracking-wide pb-2 border-b border-[#3E2F4D] flex items-center gap-1.5">
                    <i data-lucide="history" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                    <span>Activity Audit Trail</span>
                </h3>

                <div class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
                    @forelse($ticket->activities as $act)
                        <div class="p-2.5 rounded-lg bg-[#140E1B] border border-[#3E2F4D] text-[11px] space-y-1">
                            <div class="flex items-center justify-between text-slate-400 text-[10px]">
                                <span class="font-cinzel font-bold text-[#FFE894]">{{ $act->user->username ?? 'System' }}</span>
                                <span class="font-mono">{{ $act->created_at->format('M d H:i') }}</span>
                            </div>
                            <p class="text-slate-200">{{ $act->description }}</p>
                        </div>
                    @empty
                        <p class="text-[11px] text-slate-500 italic">No activity recorded yet.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
