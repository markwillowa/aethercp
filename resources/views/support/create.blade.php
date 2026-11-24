@extends('layouts.app')

@section('title', 'Submit Support Ticket')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header Card -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-cyan-600 to-blue-500 flex items-center justify-center font-bold text-slate-950 shrink-0 shadow-lg">
                <i data-lucide="plus-circle" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-wide">Submit a Support Ticket</h1>
                <p class="text-xs text-slate-400">Describe your issue in detail. Our support team will review and respond promptly.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('support.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-colors flex items-center gap-1.5 shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4 text-cyan-400"></i>
                <span>Back to Support</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0 text-rose-400"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs space-y-1 shadow-lg">
            <div class="font-bold flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-400"></i>
                <span>Please correct the errors below:</span>
            </div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Ticket Creation Form -->
    <form action="{{ route('support.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Section 1: Subject & Classification -->
        <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-6 shadow-xl space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-800">
                <i data-lucide="ticket" class="w-5 h-5 text-cyan-400"></i>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider">Ticket Information</h2>
            </div>

            <!-- Subject -->
            <div>
                <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider block mb-1">Subject *</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required placeholder="Short summary of the issue (e.g. Character stuck in Prontera Culvert)"
                       class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition-all">
            </div>

            <!-- Category, Priority & Character in 3 cols -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Category -->
                <div>
                    <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider block mb-1">Category *</label>
                    <select name="category" required class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition-all">
                        <option value="">-- Select Category --</option>
                        <option value="account" {{ old('category') === 'account' ? 'selected' : '' }}>Account Issues</option>
                        <option value="character" {{ old('category') === 'character' ? 'selected' : '' }}>Character Issues</option>
                        <option value="in_game" {{ old('category') === 'in_game' ? 'selected' : '' }}>In-Game Issues</option>
                        <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>Technical Support</option>
                        <option value="donation" {{ old('category') === 'donation' ? 'selected' : '' }}>Payment / Donation</option>
                        <option value="bug_report" {{ old('category') === 'bug_report' ? 'selected' : '' }}>Bug Reports</option>
                        <option value="report_player" {{ old('category') === 'report_player' ? 'selected' : '' }}>Report a Player</option>
                        <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <!-- Priority -->
                <div>
                    <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider block mb-1">Priority *</label>
                    <select name="priority" required class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition-all">
                        <option value="low" {{ old('priority', $settings['default_priority']) === 'low' ? 'selected' : '' }}>Low - General question</option>
                        <option value="normal" {{ old('priority', $settings['default_priority']) === 'normal' ? 'selected' : '' }}>Normal - Standard request</option>
                        <option value="high" {{ old('priority', $settings['default_priority']) === 'high' ? 'selected' : '' }}>High - Needs quick review</option>
                        <option value="urgent" {{ old('priority', $settings['default_priority']) === 'urgent' ? 'selected' : '' }}>Urgent - Critical blocker</option>
                    </select>
                </div>

                <!-- Related Character -->
                <div>
                    <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider block mb-1">Character Involved (Optional)</label>
                    <select name="character_id" class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition-all">
                        <option value="">-- None / Account-wide --</option>
                        @foreach($characters as $char)
                            <option value="{{ $char['char_id'] }}" {{ old('character_id') == $char['char_id'] ? 'selected' : '' }}>
                                {{ $char['name'] }} (Lv. {{ $char['base_level'] ?? 1 }}/{{ $char['job_level'] ?? 1 }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 2: Optional Incident Metadata -->
        <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-6 shadow-xl space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-800">
                <i data-lucide="map-pin" class="w-5 h-5 text-cyan-400"></i>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider">Incident Context (Optional)</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Transaction Ref / Order ID</label>
                    <input type="text" name="incident_ref" value="{{ old('incident_ref') }}" placeholder="e.g. PAYPAL-12345 or item ref"
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition-all">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Map Name / Coordinates</label>
                    <input type="text" name="map_name" value="{{ old('map_name') }}" placeholder="e.g. prontera, 150, 180"
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition-all">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Date &amp; Time of Incident</label>
                    <input type="datetime-local" name="incident_time" value="{{ old('incident_time') }}"
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition-all">
                </div>
            </div>
        </div>

        <!-- Section 3: Message Content -->
        <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <i data-lucide="message-square" class="w-5 h-5 text-cyan-400"></i>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider">Detailed Explanation *</h2>
                </div>
                <span class="text-[11px] text-slate-500">Provide as much detail as possible</span>
            </div>

            <div>
                <textarea name="message" rows="7" required placeholder="Describe what happened, error messages received, steps to reproduce, or any relevant details..."
                          class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 leading-relaxed transition-all">{{ old('message') }}</textarea>
            </div>

            <!-- File Attachments -->
            @if($settings['allow_attachments'])
                <div class="pt-3 border-t border-slate-800 space-y-2">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">
                        Attachments (Screenshots / Log Files)
                    </label>
                    <input type="file" name="attachments[]" multiple
                           class="block w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-cyan-400 hover:file:bg-slate-700 cursor-pointer">
                    <p class="text-[11px] text-slate-500 font-mono">
                        Max {{ $settings['max_attachments_per_msg'] }} files, up to {{ round($settings['max_attachment_size_kb'] / 1024, 1) }} MB each. Allowed formats: {{ $settings['allowed_extensions'] }}.
                    </p>
                </div>
            @endif
        </div>

        <!-- Submit Row -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('support.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 hover:from-cyan-400 hover:to-blue-500 shadow-lg shadow-cyan-500/20 cursor-pointer flex items-center gap-2">
                <i data-lucide="send" class="w-4 h-4"></i>
                <span>Submit Ticket</span>
            </button>
        </div>
    </form>
</div>
@endsection
