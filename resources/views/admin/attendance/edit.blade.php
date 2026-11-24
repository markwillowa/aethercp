@extends('layouts.admin')

@section('title', 'Edit Campaign: ' . $campaign->name . ' - Admin Panel')

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
            <h1 class="text-2xl font-bold text-white mt-1">Edit Campaign Settings</h1>
            <p class="text-xs text-slate-400 mt-0.5">Update campaign configuration, scheduling, and rules for {{ $campaign->name }}.</p>
        </div>
        <a href="{{ route('admin.attendance.schedule', $campaign->id) }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-cyan-950 hover:bg-cyan-900 text-cyan-300 border border-cyan-500/30 flex items-center gap-1.5 transition-colors">
            <i data-lucide="calendar" class="w-4 h-4"></i>
            <span>Edit Reward Schedule</span>
        </a>
    </div>

    <form method="POST" action="{{ route('admin.attendance.update', $campaign->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- General Information -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-cyan-400 border-b border-slate-800 pb-2 flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4"></i>
                <span>General Campaign Details</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Campaign Name *</label>
                    <input type="text" name="name" value="{{ old('name', $campaign->name) }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">URL Slug *</label>
                    <input type="text" name="slug" value="{{ old('slug', $campaign->slug) }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">{{ old('description', $campaign->description) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Campaign Duration (Days) *</label>
                <input type="number" name="duration_days" value="{{ old('duration_days', $campaign->duration_days) }}" min="1" max="3650" required class="w-32 bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm font-bold text-cyan-400 font-mono text-center focus:border-cyan-500 focus:outline-none">
                <span class="text-xs text-slate-400 ml-2">Days</span>
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
                        <option value="cumulative" {{ $campaign->progression_mode === 'cumulative' ? 'selected' : '' }}>Cumulative (Forgiving)</option>
                        <option value="consecutive" {{ $campaign->progression_mode === 'consecutive' ? 'selected' : '' }}>Consecutive (Streak-based)</option>
                        <option value="scheduled" {{ $campaign->progression_mode === 'scheduled' ? 'selected' : '' }}>Scheduled Calendar Days</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Missed Day Rule *</label>
                    <select name="reset_rule" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none cursor-pointer">
                        <option value="none" {{ $campaign->reset_rule === 'none' ? 'selected' : '' }}>None (Keep Progress)</option>
                        <option value="reset_to_start" {{ $campaign->reset_rule === 'reset_to_start' ? 'selected' : '' }}>Reset Streak to Day 1 on Miss</option>
                        <option value="freeze_streak" {{ $campaign->reset_rule === 'freeze_streak' ? 'selected' : '' }}>Freeze Streak</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Participation Scope *</label>
                    <select name="participation_scope" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none cursor-pointer">
                        <option value="account" {{ $campaign->participation_scope === 'account' ? 'selected' : '' }}>Account-Scoped (Recommended)</option>
                        <option value="character" {{ $campaign->participation_scope === 'character' ? 'selected' : '' }}>Character-Scoped</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-800/80">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Default Item Delivery *</label>
                    <select name="delivery_destination" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none cursor-pointer">
                        <option value="inbox" {{ $campaign->delivery_destination === 'inbox' ? 'selected' : '' }}>AetherCP Web Inbox (Safest Default)</option>
                        <option value="storage" {{ $campaign->delivery_destination === 'storage' ? 'selected' : '' }}>Direct to Kafra Storage</option>
                        <option value="character" {{ $campaign->delivery_destination === 'character' ? 'selected' : '' }}>Character Inventory</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Campaign Timezone *</label>
                    <input type="text" name="timezone" value="{{ old('timezone', $campaign->timezone) }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Daily Reset Hour (0-23)</label>
                    <input type="number" name="daily_reset_hour" value="{{ old('daily_reset_hour', $campaign->daily_reset_hour) }}" min="0" max="23" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-800/80 text-xs">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="allow_character_choice" value="1" {{ $campaign->allow_character_choice ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                    <span class="text-slate-300">Allow Player to Select Receiving Char</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="repeatable" value="1" {{ $campaign->repeatable ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                    <span class="text-slate-300">Repeatable Campaign</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="require_in_game_login" value="1" {{ $campaign->require_in_game_login ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                    <span class="text-slate-300">Require In-Game Login</span>
                </label>
            </div>
        </div>

        <!-- Scheduling & Status -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-400 border-b border-slate-800 pb-2 flex items-center gap-2">
                <i data-lucide="calendar" class="w-4 h-4"></i>
                <span>Dates, Scheduling &amp; Requirements</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Status *</label>
                    <select name="status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none cursor-pointer">
                        <option value="active" {{ $campaign->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="draft" {{ $campaign->status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="scheduled" {{ $campaign->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="paused" {{ $campaign->status === 'paused' ? 'selected' : '' }}>Paused</option>
                        <option value="completed" {{ $campaign->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Start Date</label>
                    <input type="datetime-local" name="start_at" value="{{ $campaign->start_at ? $campaign->start_at->format('Y-m-d\TH:i') : '' }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">End Date</label>
                    <input type="datetime-local" name="end_at" value="{{ $campaign->end_at ? $campaign->end_at->format('Y-m-d\TH:i') : '' }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Display Order</label>
                    <input type="number" name="display_order" value="{{ old('display_order', $campaign->display_order) }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-800/80">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Minimum Base Level</label>
                    <input type="number" name="min_base_level" value="{{ old('min_base_level', $campaign->min_base_level) }}" min="1" max="999" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Minimum Account Age (Days)</label>
                    <input type="number" name="min_account_age_days" value="{{ old('min_account_age_days', $campaign->min_account_age_days) }}" min="0" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.attendance.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-lg shadow-cyan-500/20 transition-all flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Save Changes</span>
            </button>
        </div>
    </form>
</div>
@endsection
