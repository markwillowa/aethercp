@extends('layouts.admin')

@section('title', 'Support Center Configuration')

@section('admin_content')
<div class="space-y-6">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl backdrop-blur">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 flex items-center justify-center font-bold shrink-0 shadow-inner">
                <i data-lucide="sliders" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-white tracking-wide uppercase">Support Center Configuration</h1>
                <p class="text-xs text-slate-400">Control support availability, ticket reply permissions, attachment limits, and SLA policies.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.support.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 border border-slate-700 text-slate-200 hover:bg-slate-700 hover:text-white transition-colors flex items-center gap-2 shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Back to Tickets</span>
            </a>
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

    <!-- Settings Form -->
    <form action="{{ route('admin.support.settings.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Section 1: Core Availability & Permissions -->
        <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-5 shadow-lg space-y-4 backdrop-blur">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-800">
                <i data-lucide="shield-check" class="w-5 h-5 text-cyan-400"></i>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider">Core Availability &amp; Policies</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Enable Support Center -->
                <label class="flex items-start gap-3 p-4 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 cursor-pointer transition-colors">
                    <input type="checkbox" name="enabled" value="1" {{ !empty($settings['enabled']) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 rounded border-slate-700 text-cyan-500 focus:ring-cyan-500">
                    <div>
                        <span class="text-xs font-bold text-white block">Enable Support Center</span>
                        <span class="text-[11px] text-slate-400 block mt-0.5">When disabled, players will not be able to submit new tickets or view support pages.</span>
                    </div>
                </label>

                <!-- Allow Player Replies -->
                <label class="flex items-start gap-3 p-4 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 cursor-pointer transition-colors">
                    <input type="checkbox" name="allow_player_replies" value="1" {{ !empty($settings['allow_player_replies']) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 rounded border-slate-700 text-cyan-500 focus:ring-cyan-500">
                    <div>
                        <span class="text-xs font-bold text-white block">Allow Player Replies</span>
                        <span class="text-[11px] text-slate-400 block mt-0.5">Permit players to send follow-up messages on their open tickets.</span>
                    </div>
                </label>

                <!-- Allow Ticket Reopening -->
                <label class="flex items-start gap-3 p-4 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 cursor-pointer transition-colors">
                    <input type="checkbox" name="allow_reopen" value="1" {{ !empty($settings['allow_reopen']) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 rounded border-slate-700 text-cyan-500 focus:ring-cyan-500">
                    <div>
                        <span class="text-xs font-bold text-white block">Allow Ticket Reopening</span>
                        <span class="text-[11px] text-slate-400 block mt-0.5">Permit players to reopen resolved or closed tickets if they need further assistance.</span>
                    </div>
                </label>

                <!-- Allow Player Ticket Closure -->
                <label class="flex items-start gap-3 p-4 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 cursor-pointer transition-colors">
                    <input type="checkbox" name="allow_close" value="1" {{ !empty($settings['allow_close']) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 rounded border-slate-700 text-cyan-500 focus:ring-cyan-500">
                    <div>
                        <span class="text-xs font-bold text-white block">Allow Player Ticket Closure</span>
                        <span class="text-[11px] text-slate-400 block mt-0.5">Permit players to close their own tickets once their issue is settled.</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Section 2: Ticket Capacity & Default Priority -->
        <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-5 shadow-lg space-y-4 backdrop-blur">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-800">
                <i data-lucide="layers" class="w-5 h-5 text-cyan-400"></i>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider">Capacity &amp; Priority Defaults</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider block mb-1">Max Open Tickets Per Account</label>
                    <input type="number" name="max_open_per_account" value="{{ $settings['max_open_per_account'] }}" min="1" max="20" required class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition-all">
                    <p class="text-[11px] text-slate-400 mt-1">Prevents ticket spamming by capping the number of unresolved tickets a player can maintain simultaneously.</p>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider block mb-1">Default Ticket Priority</label>
                    <select name="default_priority" class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition-all">
                        <option value="low" {{ $settings['default_priority'] === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="normal" {{ $settings['default_priority'] === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="high" {{ $settings['default_priority'] === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ $settings['default_priority'] === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Pre-selected priority on the public ticket submission form.</p>
                </div>
            </div>
        </div>

        <!-- Section 3: File Attachment Governance -->
        <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-5 shadow-lg space-y-4 backdrop-blur">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-800">
                <i data-lucide="paperclip" class="w-5 h-5 text-cyan-400"></i>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider">File Attachment Governance</h2>
            </div>

            <div class="space-y-4">
                <label class="flex items-start gap-3 p-4 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 cursor-pointer transition-colors">
                    <input type="checkbox" name="allow_attachments" value="1" {{ !empty($settings['allow_attachments']) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 rounded border-slate-700 text-cyan-500 focus:ring-cyan-500">
                    <div>
                        <span class="text-xs font-bold text-white block">Enable Attachment Uploads</span>
                        <span class="text-[11px] text-slate-400 block mt-0.5">Allow players and staff to upload screenshots and supporting documents.</span>
                    </div>
                </label>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider block mb-1">Max Size per File (KB)</label>
                        <input type="number" name="max_attachment_size_kb" value="{{ $settings['max_attachment_size_kb'] }}" min="512" max="20480" required class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition-all">
                        <p class="text-[11px] text-slate-400 mt-1">5120 KB = 5 MB per file upload.</p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider block mb-1">Max Files per Message</label>
                        <input type="number" name="max_attachments_per_msg" value="{{ $settings['max_attachments_per_msg'] }}" min="1" max="10" required class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition-all">
                        <p class="text-[11px] text-slate-400 mt-1">Allowed attachments per ticket or reply.</p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider block mb-1">Allowed File Extensions</label>
                        <input type="text" name="allowed_extensions" value="{{ $settings['allowed_extensions'] }}" required class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition-all">
                        <p class="text-[11px] text-slate-400 mt-1">Comma-separated: jpg, jpeg, png, webp, pdf, txt</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Public Announcement & Notification -->
        <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-5 shadow-lg space-y-4 backdrop-blur">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-800">
                <i data-lucide="bell" class="w-5 h-5 text-cyan-400"></i>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider">Player Announcements &amp; Notifications</h2>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider block mb-1">Support Center Notice / Instructions Banner</label>
                    <textarea name="announcement" rows="3" class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition-all">{{ $settings['announcement'] }}</textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Displayed at the top of the Player Support Center landing page.</p>
                </div>

                <label class="flex items-start gap-3 p-4 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 cursor-pointer transition-colors">
                    <input type="checkbox" name="email_notify" value="1" {{ !empty($settings['support_email_notify']) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 rounded border-slate-700 text-cyan-500 focus:ring-cyan-500">
                    <div>
                        <span class="text-xs font-bold text-white block">Email Notifications for Staff</span>
                        <span class="text-[11px] text-slate-400 block mt-0.5">Send alert emails to authorized support staff when new tickets or player replies arrive.</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.support.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-slate-800 border border-slate-700 text-slate-300 hover:text-white transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow-lg cursor-pointer flex items-center gap-2 transition-all">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Save Configuration</span>
            </button>
        </div>
    </form>
</div>
@endsection
