@extends('layouts.admin')

@section('title', 'Create Reward Campaign - Admin Panel')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.attendance.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1 font-medium">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Back to Campaigns</span>
                </a>
            </div>
            <h1 class="text-2xl font-bold text-white mt-1">Create Reward Campaign</h1>
            <p class="text-xs text-slate-400 mt-0.5">Configure campaign duration, progression rules, delivery methods, and participant eligibility.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.attendance.store') }}" class="space-y-6" x-data="{ duration: 30 }">
        @csrf

        <!-- General Information -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-cyan-400 border-b border-slate-800 pb-2 flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4"></i>
                <span>General Campaign Details</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Campaign Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. 30-Day Monthly Attendance" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">URL Slug (Optional)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" placeholder="e.g. monthly-rewards-nov-2026" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Description / Player Instructions</label>
                <textarea name="description" rows="2" placeholder="Explain how players earn rewards, requirements, and campaign lore..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">{{ old('description') }}</textarea>
            </div>

            <!-- Duration Selection (Any arbitrary positive integer) -->
            <div class="border-t border-slate-800/80 pt-4">
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                    Campaign Duration (Total Days) *
                </label>
                <div class="flex items-center gap-3">
                    <input type="number" name="duration_days" x-model.number="duration" min="1" max="3650" required class="w-32 bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm font-bold text-cyan-400 font-mono text-center focus:border-cyan-500 focus:outline-none">
                    <span class="text-xs text-slate-400">Days</span>
                </div>
                <!-- Quick Selection Presets -->
                <div class="flex items-center gap-1.5 flex-wrap mt-2">
                    <span class="text-[10px] text-slate-500 font-semibold uppercase mr-1">Quick Presets:</span>
                    @foreach([5, 7, 14, 30, 60, 90, 180, 365] as $preset)
                        <button type="button" @click="duration = {{ $preset }}" :class="duration === {{ $preset }} ? 'bg-cyan-500 text-slate-950 font-bold' : 'bg-slate-950 text-slate-400 border border-slate-800 hover:text-white hover:bg-slate-800'" class="px-2.5 py-1 rounded-lg text-xs font-mono transition-colors">
                            {{ $preset }} Days {{ $preset === 365 ? '(1 Year)' : ($preset === 90 ? '(3 Mo.)' : '') }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Attendance Progression & Delivery Rules -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-purple-400 border-b border-slate-800 pb-2 flex items-center gap-2">
                <i data-lucide="sliders" class="w-4 h-4"></i>
                <span>Progression &amp; Delivery Rules</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Progression Mode *</label>
                    <select name="progression_mode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none cursor-pointer">
                        <option value="cumulative" selected>Mode B: Cumulative Attendance (Forgiving)</option>
                        <option value="consecutive">Mode A: Consecutive Attendance (Streak-based)</option>
                        <option value="scheduled">Mode C: Scheduled Daily Calendar (Exact Dates)</option>
                    </select>
                    <p class="text-[10px] text-slate-500 mt-1">Cumulative allows missing days without losing prior progress.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Missed Day Rule *</label>
                    <select name="reset_rule" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none cursor-pointer">
                        <option value="none" selected>None (Keep Progress / Freeze Streak)</option>
                        <option value="reset_to_start">Reset Streak to Day 1 on Missed Day</option>
                        <option value="freeze_streak">Freeze Streak</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Participation Scope *</label>
                    <select name="participation_scope" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none cursor-pointer">
                        <option value="account" selected>Account-Scoped (Recommended)</option>
                        <option value="character">Character-Scoped</option>
                    </select>
                    <p class="text-[10px] text-slate-500 mt-1">Account-scoped prevents claiming duplicate daily rewards across multiple chars.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-800/80">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Default Item Delivery *</label>
                    <select name="delivery_destination" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none cursor-pointer">
                        <option value="inbox" selected>AetherCP Web Inbox (Safest Default)</option>
                        <option value="storage">Direct to Kafra Storage</option>
                        <option value="character">Character Inventory (Requires Offline)</option>
                    </select>
                    <p class="text-[10px] text-slate-500 mt-1">Zeny is always credited directly to selected character's balance.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Campaign Timezone *</label>
                    <input type="text" name="timezone" value="UTC" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                    <p class="text-[10px] text-slate-500 mt-1">Server timezone for daily rollover (e.g. UTC, Asia/Manila, America/New_York).</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Daily Reset Hour (0-23)</label>
                    <input type="number" name="daily_reset_hour" value="0" min="0" max="23" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                    <p class="text-[10px] text-slate-500 mt-1">0 = Midnight, 4 = 4:00 AM server time.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-800/80 text-xs">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="allow_character_choice" value="1" checked class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                    <span class="text-slate-300">Allow Player to Select Receiving Char</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="repeatable" value="1" class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                    <span class="text-slate-300">Repeatable Campaign (Cycles on completion)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="require_in_game_login" value="1" class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                    <span class="text-slate-300">Require In-Game Login Activity</span>
                </label>
            </div>
        </div>

        <!-- Scheduling & Eligibility -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-400 border-b border-slate-800 pb-2 flex items-center gap-2">
                <i data-lucide="calendar" class="w-4 h-4"></i>
                <span>Dates, Scheduling &amp; Requirements</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Status *</label>
                    <select name="status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none cursor-pointer">
                        <option value="active" selected>Active</option>
                        <option value="draft">Draft (Hidden from players)</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="paused">Paused</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Start Date (Optional)</label>
                    <input type="datetime-local" name="start_at" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">End Date (Optional)</label>
                    <input type="datetime-local" name="end_at" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Display Order</label>
                    <input type="number" name="display_order" value="0" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-800/80">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Minimum Base Level</label>
                    <input type="number" name="min_base_level" value="1" min="1" max="999" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                    <p class="text-[10px] text-slate-500 mt-1">1 = Any adventurer can claim.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Minimum Account Age (Days)</label>
                    <input type="number" name="min_account_age_days" value="0" min="0" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                    <p class="text-[10px] text-slate-500 mt-1">0 = New accounts eligible immediately.</p>
                </div>
            </div>

            <!-- Auto-Schedule Generator Checkbox -->
            <div class="p-3.5 rounded-xl bg-cyan-950/30 border border-cyan-500/30 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <i data-lucide="sparkles" class="w-5 h-5 text-cyan-400 shrink-0"></i>
                    <div>
                        <strong class="text-xs text-white block">Auto-Generate Baseline Reward Schedule</strong>
                        <span class="text-[11px] text-slate-400">Generates all days automatically with progressive Zeny, Potions, and weekly Milestone boxes so you don't have to build 30 or 365 days by hand.</span>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" name="auto_generate_schedule" value="1" checked class="sr-only peer">
                    <div class="w-9 h-5 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-cyan-500"></div>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.attendance.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow-lg shadow-cyan-500/20 transition-all flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Save &amp; Build Reward Schedule</span>
            </button>
        </div>
    </form>
</div>
@endsection
