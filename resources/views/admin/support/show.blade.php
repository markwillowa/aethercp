@extends('layouts.admin')

@section('title', 'Manage Ticket #' . $ticket->ticket_reference)

@section('admin_content')
<div class="space-y-6" x-data="{ isInternalNote: false }">

    <!-- Top Navigation & Summary Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl backdrop-blur">
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('admin.support.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-bold flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>All Tickets</span>
                </a>
                <span class="text-slate-600">•</span>
                <span class="text-xs font-mono font-bold text-slate-300">{{ $ticket->ticket_reference }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-wide uppercase mt-1">
                {{ $ticket->subject }}
            </h1>
            <div class="flex items-center gap-2 mt-2 flex-wrap text-xs">
                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono border {{ $ticket->status_badge_class }}">
                    {{ $ticket->status_label }}
                </span>
                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono border {{ $ticket->priority_badge_class }}">
                    {{ $ticket->priority_label }}
                </span>
                <span class="px-2.5 py-0.5 rounded text-[10px] bg-slate-950 text-slate-300 border border-slate-800">
                    {{ $ticket->category_label }}
                </span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-400">Assigned:</span>
            <span class="font-bold text-cyan-400">
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
                <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="message-square" class="w-4 h-4 text-cyan-400"></i>
                    <span>Ticket Conversation &amp; Internal Notes</span>
                </h3>

                <div class="space-y-4">
                    @foreach($ticket->messages as $msg)
                        @php
                            $isInternal = $msg->is_internal;
                            $isStaff = ($msg->sender_type === 'staff');
                        @endphp

                        <div class="p-5 rounded-2xl transition-all shadow-md {{ $isInternal ? 'bg-amber-950/20 border-2 border-amber-500/50' : ($isStaff ? 'bg-slate-900 border border-cyan-500/40 shadow-cyan-950/20' : 'bg-slate-950 border border-slate-800') }}">
                            <!-- Sender Header -->
                            <div class="flex items-center justify-between pb-3 border-b {{ $isInternal ? 'border-amber-500/30' : ($isStaff ? 'border-slate-800' : 'border-slate-800/60') }}">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 {{ $isInternal ? 'bg-amber-500/20 border border-amber-500 text-amber-300' : ($isStaff ? 'bg-cyan-500/20 border border-cyan-500 text-cyan-400' : 'bg-slate-800 border border-slate-700 text-slate-200') }}">
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
                                            <span class="font-bold text-xs {{ $isInternal ? 'text-amber-300' : ($isStaff ? 'text-cyan-400' : 'text-white') }}">{{ $msg->sender_name }}</span>
                                            @if($isInternal)
                                                <span class="px-2 py-0.5 rounded text-[9px] font-bold font-mono uppercase bg-amber-500/20 text-amber-300 border border-amber-500/40">
                                                    INTERNAL NOTE • STAFF ONLY
                                                </span>
                                            @elseif($isStaff)
                                                <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-cyan-500/10 text-cyan-300 border border-cyan-500/30">
                                                    Support Staff
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-[9px] font-bold font-mono bg-slate-800 text-slate-400 border border-slate-700">
                                                    Player
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <span class="text-[10px] text-slate-400 font-mono">
                                    {{ $msg->created_at?->format('M d, Y H:i:s') ?? 'N/A' }}
                                </span>
                            </div>

                            <!-- Message Content -->
                            <div class="pt-3.5 text-xs text-slate-200 leading-relaxed whitespace-pre-wrap font-sans">
                                {{ $msg->message }}
                            </div>

                            <!-- Attachments -->
                            @if($msg->attachments->count() > 0)
                                <div class="mt-4 pt-3 border-t {{ $isInternal ? 'border-amber-500/30' : 'border-slate-800' }} space-y-2">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Attachments:</span>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($msg->attachments as $att)
                                            <a href="{{ route('admin.support.attachment', ['id' => $ticket->id, 'attachmentId' => $att->id]) }}" 
                                               class="px-3 py-1.5 rounded-lg text-xs bg-slate-950 border border-slate-800 hover:border-cyan-500 text-slate-300 hover:text-white flex items-center gap-2 transition-colors">
                                                <i data-lucide="{{ $att->is_image ? 'image' : 'file-text' }}" class="w-3.5 h-3.5 text-cyan-400"></i>
                                                <span class="font-medium truncate max-w-[180px]">{{ $att->original_filename }}</span>
                                                <span class="text-[10px] text-slate-500 font-mono">({{ $att->formatted_size }})</span>
                                                <i data-lucide="download" class="w-3 h-3 ml-1 text-slate-500"></i>
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
            <div class="rounded-2xl p-6 transition-all border-2 backdrop-blur shadow-xl" :class="isInternalNote ? 'bg-amber-950/20 border-amber-500/70' : 'bg-slate-900/80 border-slate-800'">
                <div class="flex items-center justify-between mb-3 pb-2 border-b" :class="isInternalNote ? 'border-amber-500/30' : 'border-slate-800'">
                    <h4 class="text-xs font-bold uppercase tracking-wider flex items-center gap-2" :class="isInternalNote ? 'text-amber-300' : 'text-cyan-400'">
                        <i data-lucide="edit-3" class="w-4 h-4" :class="isInternalNote ? 'text-amber-400' : 'text-cyan-400'"></i>
                        <span x-text="isInternalNote ? 'Add Internal Staff Note (Confidential)' : 'Post Staff Response to Player'"></span>
                    </h4>

                    <!-- Note Mode Checkbox Toggle -->
                    <label class="flex items-center gap-2 text-xs cursor-pointer select-none px-3 py-1 rounded-lg border transition-colors" :class="isInternalNote ? 'bg-amber-500/20 border-amber-500/50 text-amber-300' : 'bg-slate-950 border-slate-800 text-slate-300 hover:text-white'">
                        <input type="checkbox" name="is_internal" x-model="isInternalNote" class="rounded text-amber-500 focus:ring-0">
                        <span class="font-bold">Internal Note Mode</span>
                    </label>
                </div>

                <form action="{{ route('admin.support.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="is_internal" :value="isInternalNote ? 1 : 0">

                    <div>
                        <textarea name="message" rows="4" required :placeholder="isInternalNote ? 'Record confidential notes for other administrators and GMs (never visible to the player)...' : 'Type your official support response to the player...'" 
                                  class="w-full p-4 bg-slate-950 border rounded-xl text-xs font-medium text-white placeholder:text-slate-500 focus:outline-none transition-all leading-relaxed"
                                  :class="isInternalNote ? 'border-amber-500/60 focus:border-amber-400' : 'border-slate-800 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500'"></textarea>
                    </div>

                    <!-- File Attachments -->
                    @if($settings['allow_attachments'])
                        <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-300 flex items-center gap-1.5">
                                <i data-lucide="paperclip" class="w-3.5 h-3.5 text-cyan-400"></i>
                                <span>Attach Staff Documentation / Proof Files</span>
                            </label>
                            <input type="file" name="attachments[]" multiple 
                                   class="block w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 cursor-pointer">
                        </div>
                    @endif

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="py-2.5 px-6 rounded-xl text-xs font-bold tracking-wide shadow-md flex items-center gap-2 cursor-pointer transition-all" :class="isInternalNote ? 'bg-amber-500 hover:bg-amber-400 text-black' : 'bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950'">
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
            <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-4 backdrop-blur">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2 pb-2 border-b border-slate-800">
                    <i data-lucide="sliders" class="w-4 h-4 text-cyan-400"></i>
                    <span>Ticket Management</span>
                </h3>

                <!-- Update Status Form -->
                <form action="{{ route('admin.support.update', $ticket->id) }}" method="POST" class="space-y-2">
                    @csrf
                    <label class="block text-[11px] font-semibold text-slate-300">Case Status</label>
                    <div class="flex items-center gap-2">
                        <select name="status" class="flex-1 px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 cursor-pointer">
                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="awaiting_player" {{ $ticket->status === 'awaiting_player' ? 'selected' : '' }}>Awaiting Player</option>
                            <option value="awaiting_staff" {{ $ticket->status === 'awaiting_staff' ? 'selected' : '' }}>Awaiting Staff</option>
                            <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                        <button type="submit" class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-800 border border-slate-700 text-slate-200 hover:bg-slate-700 hover:text-white transition-colors cursor-pointer">
                            Update
                        </button>
                    </div>
                </form>

                <!-- Update Priority Form -->
                <form action="{{ route('admin.support.update', $ticket->id) }}" method="POST" class="space-y-2">
                    @csrf
                    <label class="block text-[11px] font-semibold text-slate-300">Priority Level</label>
                    <div class="flex items-center gap-2">
                        <select name="priority" class="flex-1 px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 cursor-pointer">
                            <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="normal" {{ $ticket->priority === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        <button type="submit" class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-800 border border-slate-700 text-slate-200 hover:bg-slate-700 hover:text-white transition-colors cursor-pointer">
                            Update
                        </button>
                    </div>
                </form>

                <!-- Assign Staff Form -->
                <form action="{{ route('admin.support.update', $ticket->id) }}" method="POST" class="space-y-2">
                    @csrf
                    <label class="block text-[11px] font-semibold text-slate-300">Assigned Staff</label>
                    <div class="flex items-center gap-2">
                        <select name="assigned_staff_id" class="flex-1 px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 cursor-pointer">
                            <option value="">Unassigned</option>
                            @foreach($staffMembers as $sMember)
                                <option value="{{ $sMember->id }}" {{ $ticket->assigned_staff_id == $sMember->id ? 'selected' : '' }}>
                                    {{ $sMember->username }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-800 border border-slate-700 text-slate-200 hover:bg-slate-700 hover:text-white transition-colors cursor-pointer">
                            Assign
                        </button>
                    </div>
                </form>
            </div>

            <!-- Player & Ticket Details Card -->
            <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-3 text-xs backdrop-blur">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider pb-2 border-b border-slate-800">
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
                            <span class="font-bold text-cyan-400">{{ $ticket->char_name }} (ID #{{ $ticket->char_id }})</span>
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
                    <div class="flex justify-between pt-2 border-t border-slate-800">
                        <span class="text-slate-400">Created:</span>
                        <span class="font-mono text-slate-300">{{ $ticket->created_at?->format('M d, Y H:i') ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Activity Audit Trail -->
            <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-3 backdrop-blur">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider pb-2 border-b border-slate-800 flex items-center gap-1.5">
                    <i data-lucide="history" class="w-3.5 h-3.5 text-cyan-400"></i>
                    <span>Activity Audit Trail</span>
                </h3>

                <div class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
                    @forelse($ticket->activities as $act)
                        <div class="p-2.5 rounded-lg bg-slate-950 border border-slate-800 text-[11px] space-y-1">
                            <div class="flex items-center justify-between text-slate-400 text-[10px]">
                                <span class="font-bold text-cyan-300">{{ $act->user->username ?? 'System' }}</span>
                                <span class="font-mono">{{ $act->created_at?->format('M d H:i') ?? 'N/A' }}</span>
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
